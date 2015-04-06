<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ImdbPersonRepository")
 * @ORM\Table(name="person_imdb", 
 *      uniqueConstraints={@ORM\UniqueConstraint(name="imdb_person_unique",columns={"imdbId"})}
 * )
 */
class ImdbPerson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     **/
    protected $person;

    /**
     * @ORM\Column(
     *     type="integer"
     * )
     * @Assert\NotBlank()
     */
    protected $imdbId;

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
     * Set imdbId
     *
     * @param integer $imdbId
     * @return ImdbPerson
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return integer 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     * @return ImdbPerson
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
}
