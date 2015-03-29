<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;


/**
 * @ORM\Entity
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MovieRepository")
 * @GRID\Source(columns="id, imdbId, year, title")
 */
class Movie
{
    const TYPE_CINEMA     = 1;
    const TYPE_TELEVISION = 2;
    const TYPE_THEATRE    = 3;
    const TYPE_COMMERCIAL = 4;
    const TYPE_ALL        = 5;
    const TYPE_VIDEO      = 6;

    const FORMAT_MOVIE        = 1;
    const FORMAT_FEATURE_FILM = 1;
    const FORMAT_SERIES       = 2;
    const FORMAT_EPISODE      = 2;
    const FORMAT_SHORT        = 3;
    const FORMAT_TVFEATURE    = 5;
    const FORMAT_TVEPISODE    = 6;
    const FORMAT_DOCUMENTARY  = 7;
    const FORMAT_DOCUEPISODE  = 8;
    const FORMAT_DOCUEPISODES = 9;
    const FORMAT_DOCUSERIES   = 10;
    const FORMAT_MUSICVIDEO   = 11;
    const FORMAT_ADVERTIZING  = 12;
    const FORMAT_IMAGEFILM    = 13;
    const FORMAT_INDUSTRIAL   = 14;
    const FORMAT_REPORT       = 15;
    const FORMAT_TVSHOW       = 16;
    const FORMAT_TVMAGAZINE   = 17;
    const FORMAT_NEWS         = 18;
    const FORMAT_TVEVENT      = 19;
    const FORMAT_TVEPISODES   = 20;
    const FORMAT_VIDEO_GAME   = 21;
    const FORMAT_MINI_SERIES  = 22;
  
    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    protected $id;

    /**
    * @ORM\Column(type="integer")
    */
    protected $imdbId;

    /**
    * @ORM\Column(type="integer")
    */
    protected $year;

    /**
    * @ORM\Column(type="string")
    */
    protected $title;

    /**
    * @ORM\Column(type="string", nullable = true)
    */
    protected $poster;

    /**
    * @ORM\ManyToMany(targetEntity="Genre", inversedBy="movies")
    * @ORM\JoinTable(name="movie_genre_ref",
    *      joinColumns={@ORM\JoinColumn(name="movie_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
    *      )
    */
    protected $genres;

    /**
     * @ORM\OneToMany(targetEntity="PersonMovieRef", mappedBy="movie")
     */
    protected $refs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->genres = new ArrayCollection();
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
     * Set imdbId
     *
     * @param integer $imdbId
     * @return Movie
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
     * Set title
     *
     * @param string $title
     * @return Movie
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add genres
     *
     * @param \AppBundle\Entity\Genre $genres
     * @return Movie
     */
    public function addGenre(\AppBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \AppBundle\Entity\Genre $genres
     */
    public function removeGenre(\AppBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    public function clearGenres()
    {
        $genres = $this->getGenres();
        foreach ($genres as $genre) {
            $this->removeGenre($genre);
        }
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set poster
     *
     * @param string $poster
     * @return Movie
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Get poster
     *
     * @return string 
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Add refs
     *
     * @param \AppBundle\Entity\PersonMovieRef $refs
     * @return Movie
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
}
