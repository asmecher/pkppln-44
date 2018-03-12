<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Blacklist;
use AppBundle\Entity\Whitelist;
use Doctrine\ORM\EntityRepository;

/**
 * Custom journal queries for doctrine.
 */
class JournalRepository extends EntityRepository {

    /**
     * Get a list of journals that need to be pinged.
     * 
     * @return Collection|Journal[]
     *   List of journals.
     */
    public function getJournalsToPing() {
        
        $blacklist = $this->getEntityManager()->getRepository(Blacklist::class)
            ->createQueryBuilder('bl')
          ->select('bl.uuid');
        
        $whitelist = $this->getEntityManager()->getRepository(Whitelist::class)
            ->createQueryBuilder('wl')
          ->select('wl.uuid');
        
        $qb = $this->createQueryBuilder('j');
        $qb->andWhere('j.status != :status');
        $qb->setParameter('status', 'ping-error');
        $qb->andWhere($qb->expr()->notIn('j.uuid', $blacklist->getDQL()));
        $qb->andWhere($qb->expr()->notIn('j.uuid', $whitelist->getDQL()));
        return $qb->getQuery()->execute();
    }
    
}
