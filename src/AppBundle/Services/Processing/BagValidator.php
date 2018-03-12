<?php

namespace AppBundle\Services\Processing;

use AppBundle\Entity\Deposit;
use AppBundle\Services\FilePaths;
use BagIt;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Validate a bag, according to the bagit spec.
 */
class BagValidator {

    /**
     * @var FilePaths
     */
    private $filePaths;

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     *
     * @param FilePaths $fp
     * @param Filesystem $fs
     */
    public function __construct(FilePaths $fp, Filesystem $fs) {
        $this->filePaths = $fp;
        $this->fs = $fs;
    }

    /**
     * {@inheritdoc}
     */
    public function processDeposit(Deposit $deposit) {
        $harvestedPath = $this->filePaths->getHarvestFile($deposit);

        if (!$this->fs->exists($harvestedPath)) {
            throw new Exception("Deposit file {$harvestedPath} does not exist");
        }

        $bag = new BagIt($harvestedPath);
        $bag->validate();

        if (count($bag->getBagErrors()) > 0) {
            foreach ($bag->getBagErrors() as $error) {
                $deposit->addErrorLog("Bagit validation error for {$error[0]} - {$error[1]}");
            }
            throw new Exception("BagIt validation failed for {$deposit->getDepositUuid()}");
        }
        $journalVersion = $bag->getBagInfoData('PKP-PLN-OJS-Version');
        if ($journalVersion && $journalVersion !== $deposit->getJournalVersion()) {
            $deposit->addErrorLog("Bag journal version tag {$journalVersion} does not match deposit journal version {$deposit->getJournalVersion()}");
        }

        return true;
    }

}
