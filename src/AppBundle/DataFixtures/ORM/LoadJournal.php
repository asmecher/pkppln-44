<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Journal;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadJournal form.
 */
class LoadJournal extends Fixture {

    const UUIDS = array(
        '44428B12-CDC4-453E-8157-319004CD8CE6',
        '04F2C06E-35B8-43C1-B60C-1934271B0B7E',
        'CBF45637-5D69-44C3-AEC0-A906CBC3E27B',
        '9934C273-8319-4816-92DA-6EEADA91DCAC',
    );
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Journal();
            $fixture->setUuid(self::UUIDS[$i]);
            $fixture->setContacted(new DateTime("2018-{$i}-{$i}T12:00:00"));
            $fixture->setOjsVersion('2.4.8.' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setIssn('1234-123'.$i);
            $fixture->setUrl('http://example.com/journal/' . $i);
            $fixture->setStatus('healthy');
            $fixture->setTermsAccepted(true);
            $fixture->setEmail('email@example.com');
            $fixture->setPublisherName('PublisherName ' . $i);
            $fixture->setPublisherUrl('http://example.com/publisher/' . $i);

            $em->persist($fixture);
            $this->setReference('journal.' . $i, $fixture);
        }

        $em->flush();
    }

}