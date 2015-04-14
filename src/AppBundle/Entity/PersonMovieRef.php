<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PersonMovieRefRepository")
 * @ORM\Table(name="person_movie_ref")
 *
 */
class PersonMovieRef
{
    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    protected $id;

    /**
    * @ORM\ManyToOne(targetEntity="Person", inversedBy="refs")
    * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
    **/
    private $person;

  /**
   * @ORM\ManyToOne(targetEntity="Job")
   * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
   **/
  protected $job;
  
  /**
   * @ORM\Column(type="string", nullable=true)
   */
  protected $role;

  /**
   * @ORM\ManyToOne(targetEntity="Movie", inversedBy="refs")
   * @ORM\JoinColumn(name="movie_id", referencedColumnName="id")
   **/
  protected $movie;

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
     * Set role
     *
     * @param string $role
     * @return PersonMovieRef
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return PersonMovieRef
     */
    public function setPerson(\AppBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \AppBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set job
     *
     * @param \AppBundle\Entity\Job $job
     * @return PersonMovieRef
     */
    public function setJob(\AppBundle\Entity\Job $job = null)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return \AppBundle\Entity\Job 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set movie
     *
     * @param \AppBundle\Entity\Movie $movie
     * @return PersonMovieRef
     */
    public function setMovie(\AppBundle\Entity\Movie $movie = null)
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * Get movie
     *
     * @return \AppBundle\Entity\Movie 
     */
    public function getMovie()
    {
        return $this->movie;
    }
}
