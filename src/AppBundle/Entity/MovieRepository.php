<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

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
}