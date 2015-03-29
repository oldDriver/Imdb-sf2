<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Entity\Job as PersonJob;

/**
 * @DI\Service("job.service", public=true)
 */
class Job
{
    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     * })
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getJob($job = Job::ACTOR_MALE)
    {
        $repo = $this->em->getRepository('AppBundle:Job');
        $object = $repo->findOneBy(array('job' => $job));
        if (empty($object)) {
            $object = new PersonJob();
            $object->setJob($job);
            $this->em->persist($object);
            $this->em->flush();
        }
        return $object;
    }
}