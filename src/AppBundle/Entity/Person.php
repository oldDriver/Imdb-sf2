<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints AS Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PersonRepository")
 * @ORM\Table(name="person")
 * @GRID\Source(columns="id, name, birthAt, deathAt")
 */
class Person
{
    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    protected $id;

    /**
    * @ORM\Column(type="string")
    * @Assert\NotBlank()
    */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $photo;

    /**
    * @ORM\Column(type="date", nullable=true)
    * @Assert\Date()
    */
    protected $birthAt;

    /**
    * @ORM\Column(type="date", nullable=true)
    * @Assert\Date()
    */
    protected $deathAt;

    /**
     * @ORM\OneToMany(targetEntity="ImdbPerson", mappedBy="person")
     * )
     */
    protected $imdbIds;

    /**
    * @ORM\ManyToMany(
    *  targetEntity="Job",
    *  inversedBy="persons"
    * )
    * @ORM\JoinTable(
    *  name="person_job_ref",
    *  joinColumns={
    *    @ORM\JoinColumn(name="person_id",
    *    referencedColumnName="id"
    *    )
    *  },
    *  inverseJoinColumns={
    *    @ORM\JoinColumn(
    *      name="job_id", referencedColumnName="id"
    *    )
    *  }
    * )
    */
    protected $jobs;

    /**
     * @ORM\OneToMany(targetEntity="PersonMovieRef", mappedBy="person")
     */
    protected $refs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->refs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->imdbIds = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Person
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set birthAt
     *
     * @param \DateTime $birthAt
     * @return Person
     */
    public function setBirthAt($birthAt)
    {
        $this->birthAt = $birthAt;

        return $this;
    }

    /**
     * Get birthAt
     *
     * @return \DateTime 
     */
    public function getBirthAt()
    {
        return $this->birthAt;
    }

    /**
     * Set deathAt
     *
     * @param \DateTime $deathAt
     * @return Person
     */
    public function setDeathAt($deathAt)
    {
        $this->deathAt = $deathAt;

        return $this;
    }

    /**
     * Get deathAt
     *
     * @return \DateTime 
     */
    public function getDeathAt()
    {
        return $this->deathAt;
    }

    /**
     * Add jobs
     *
     * @param \AppBundle\Entity\Job $jobs
     * @return Person
     */
    public function addJob(\AppBundle\Entity\Job $jobs)
    {
        $this->jobs[] = $jobs;

        return $this;
    }

    /**
     * Remove jobs
     *
     * @param \AppBundle\Entity\Job $jobs
     */
    public function removeJob(\AppBundle\Entity\Job $jobs)
    {
        $this->jobs->removeElement($jobs);
    }

    /**
     * Get jobs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    public function clearJobs()
    {
        $jobs = $this->getJobs();
        foreach ($jobs as $job) {
            $this->removeJob($job);
        }
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Person
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Add refs
     *
     * @param \AppBundle\Entity\PersonMovieRef $refs
     * @return Person
     */
    public function addRef(\AppBundle\Entity\PersonMovieRef $refs)
    {
        $this->refs[] = $refs;

        return $this;
    }

    /**
     * Remove refs
     *
     * @param \AppBundle\Entity\PersonMovieRef $refs
     */
    public function removeRef(\AppBundle\Entity\PersonMovieRef $refs)
    {
        $this->refs->removeElement($refs);
    }

    /**
     * Get refs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRefs()
    {
        return $this->refs;
    }

    /**
     * 
     * @return string
     */
    public function getPermalink()
    {
        $link = array();
        $link[] = TextTools::stripText($this->getName());
        $link[] = self::LINK_LETTER;
        $link[] = $this->getId();
        return implode('_', $link);
    }

    /**
     * Add imdbIds
     *
     * @param \AppBundle\Entity\PersonImdb $imdbIds
     * @return Person
     */
    public function addImdbId(\AppBundle\Entity\ImdbPerson $imdbIds)
    {
        $this->imdbIds[] = $imdbIds;

        return $this;
    }

    /**
     * Remove imdbIds
     *
     * @param \AppBundle\Entity\PersonImdb $imdbIds
     */
    public function removeImdbId(\AppBundle\Entity\ImdbPerson $imdbIds)
    {
        $this->imdbIds->removeElement($imdbIds);
    }

    /**
     * Get imdbIds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImdbIds()
    {
        return $this->imdbIds;
    }
}
