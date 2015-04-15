<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class MovieRepository extends EntityRepository
{
    public function lastRecord()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m.imdbId FROM AppBundle:Movie m WHERE m.queue = 1 ORDER BY m.imdbId DESC'
            )
            ->setMaxResults(1)
            ->getScalarResult();
    }

    /**
     * 
     * @param integer $id
     * @return \Doctrine\ORM\mixed
     */
    public function getDetails($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT m, g, p, r, j
            FROM AppBundle:Movie m
            LEFT JOIN m.genres g
            LEFT JOIN m.refs r
            JOIN r.person p
            JOIN r.job j
            WHERE m.id = :id
        ');
        $query->setParameter('id', $id);
        return $query->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}