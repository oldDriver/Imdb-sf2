<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Entity\GenreRepository")
 * @ORM\Table(name="job")
 */
class Job
{
    /**
     * @var string
     */
    const ACTOR_MALE = 'Actor';

    /**
     * @var string
     */
    const ACTOR_FEMALE = 'Actress';

    /**
     * @var string
     */
    const DIRECTOR = 'Director';

    /**
     * @var string
     */
    const WRITER = 'Writer';

    /**
     * @var string
     */
    const PRODUCER = 'Producer';

    /**
     * @var string
     */
    const CINEMATOGRAPHER = 'Cinematographer';

    /**
     * @var string
     */
    const COMPOSER = 'Composer';

    /**
     * @var string
     */
    const EDITOR = 'Editor';

    /**
     * @var string
     */
    const OTHER = 'Other';

    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    protected $id;

    /**
    * @ORM\Column(type="string")
    */
    protected $job;

    /**
    * @ORM\ManyToMany(targetEntity="Person", mappedBy="jobs")
    */
    protected $persons;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set job
     *
     * @param string $job
     * @return Job
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return string 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Add persons
     *
     * @param \AppBundle\Entity\Person $persons
     * @return Job
     */
    public function addPerson(\AppBundle\Entity\Person $persons)
    {
        $this->persons[] = $persons;

        return $this;
    }

    /**
     * Remove persons
     *
     * @param \AppBundle\Entity\Person $persons
     */
    public function removePerson(\AppBundle\Entity\Person $persons)
    {
        $this->persons->removeElement($persons);
    }

    /**
     * Get persons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersons()
    {
        return $this->persons;
    }
}
