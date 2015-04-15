<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query as Query;

class JobRepository extends EntityRepository
{
    public function listJobsAsc()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT j FROM AppBundle\Entity\Job j ORDER BY j.job ASC'
        );
        /**
         * Result
         */
        return $query->getResult(Query::HYDRATE_ARRAY); 
    }
}