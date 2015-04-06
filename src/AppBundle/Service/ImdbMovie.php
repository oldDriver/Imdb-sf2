<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use AppBundle\Util\Imdb\ImdbMoviePage;
use AppBundle\Util\Imdb\ImdbMovieCastPage;
use AppBundle\Util\Imdb\ImdbPersonPage;
use AppBundle\Util\Imdb\ImdbAbstractPage;
use AppBundle\Util\Imdb\ImdbPageInterface;
use AppBundle\Util\Imdb\ImdbPersonPageInterface;

use AppBundle\Entity\Movie;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Person;
use AppBundle\Entity\Job;
use AppBundle\Entity\PersonMovieRef;
use AppBundle\Rntity\ImdbPerson;
use AppBundle\AppBundle;

/**
 * @DI\Service("imdb.movie", public=true)
 */
class ImdbMovie
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * 
     * @var unknown
     */
    private $container;

    /**
     * @var \AppBundle\Entity\MovieRepository
     */
    private $repoMovie;

    /**
     * @var \AppBundle\Entity\PersonRepository
     */
    private $repoPerson;

    /**
     * @var \AppBundle\Entity\ImdbPersonRepository
     */
    private $repoImdb;

    /**
     * @var \AppBundle\Entity\GenreRepository
     */
    private $repoGenre;

    /**
     * @var \AppBundle\Entity\JobRepository
     */
    private $repoJob;

    /**
     * @var \AppBundle\Entity\PersonMovieRefRepository
     */
    private $repoRef;

    /**
     * @var \AppBundle\Entity\Job
     */
    private $jobActor;

    /**
     * @var \AppBundle\Entity\Job
     */
    private $jobActress;

    /**
     * @var \AppBundle\Entity\Job
     */
    private $jobDirector;

    /**
     * @var \AppBundle\Entity\Job
     */
    private $jobWriter;

    /**
     * @var AppBundle\Entity\Job
     */
    private $jobProducer;

    /**
     * @var AppBundle\Entity\Job
     */
    private $jobCinematographer;

    /**
     * @var AppBundle\Entity\Job
     */
    private $jobComposer;

    /**
     * @var AppBundle\Entity\Job
     */
    private $jobEditor;

    /**
     * @var AppBundle\Entity\Job
     */
    private $jobOther;

    /**
     * @var AppBundle\Util\Imdb\ImdbPersonPage
     */
    private $imdbPerson;

    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->prepareService();
    }

    private function prepareService()
    {
        $this->repoMovie = $this->em->getRepository('AppBundle:Movie');
        $this->repoPerson = $this->em->getRepository('AppBundle:Person');
        $this->repoGenre = $this->em->getRepository('AppBundle:Genre');
        $this->repoJob = $this->em->getRepository('AppBundle:Job');
        $this->repoRef = $this->em->getRepository('AppBundle:PersonMovieRef');
        $this->repoImdb = $this->em->getRepository('AppBundle:ImdbPerson');
        
        $this->jobActor = $this->container->get('job.service')->getJob(Job::ACTOR_MALE);
        $this->jobActress = $this->container->get('job.service')->getJob(Job::ACTOR_FEMALE);
        $this->jobDirector = $this->container->get('job.service')->getJob(Job::DIRECTOR);
        $this->jobWriter = $this->container->get('job.service')->getJob(Job::WRITER);
        $this->jobProducer = $this->container->get('job.service')->getJob(Job::PRODUCER);
        $this->jobOther = $this->container->get('job.service')->getJob(Job::OTHER);
        $this->jobCinematographer = $this->container->get('job.service')->getJob(Job::CINEMATOGRAPHER);
        $this->jobComposer = $this->container->get('job.service')->getJob(Job::COMPOSER);
        $this->jobEditor = $this->container->get('job.service')->getJob(Job::EDITOR);
    }

    /**
     * @param number $start
     * @param number $end
     */
    public function importMovies($start = 1, $end = 1)
    {
        for ($imdbId = $start; $imdbId <= $end; $imdbId++) {
            $this->importOneMovie($imdbId);
        }
    }

    /**
     * @param number $imdbId
     * @return boolean
     */
    public function importOneMovie($imdbId, $queue = true)
    {
        $imdbMovie = new ImdbMoviePage();
        $imdbMovie->setId($imdbId);
        if ($imdbMovie->isMovie() && $imdbMovie->isValid()) {
            $movie = $this->repoMovie->findOneBy(array('imdbId' => $imdbId));
            if (empty($movie)) {
                $movie = new Movie();
            }
            $movie->setTitle($imdbMovie->getTitle());
            $movie->setImdbId($imdbId);
            $movie->setYear($imdbMovie->getYear());
            $movie->setPoster($imdbMovie->getPoster());
            $movie->clearGenres();
            $genres = $imdbMovie->getGenre();
            if (is_array($genres) && !empty($genres)) {
                foreach ($genres as $text) {
                    $genre = $this->repoGenre->findOneBy(array('genre' => $text));
                    if (empty($genre)) {
                        $genre = new Genre();
                        $genre->setGenre($text);
                        $this->em->persist($genre);
                        $this->em->flush();
                    }
                    $movie->addGenre($genre);
                }
            }
            $movie->setQueue($queue);
            $this->em->persist($movie);
            $this->em->flush();
            // Clear refs
            $refs = $this->repoRef->findBy(array('movie' => $movie));
            foreach ($refs as $ref) {
                if ($ref instanceof Movie) {
                    $this->em->remove($ref);
                }
            }
            $this->em->flush();
            // Now we have movie
            $page = new ImdbMovieCastPage();
            $page->setId($imdbId);
            $actors = $page->getActors();
            foreach ($actors as $actor) {
                if (empty($actor['imdbId'])) {
                    continue;
                }
                $person = $this->checkPersonByImdb($actor['imdbId']);
                $person->clearJobs();
                $jobs = $this->imdbPerson->getJobs();
                if (in_array(Job::ACTOR_FEMALE, $jobs)) {
                    $movieJob = $this->jobActress;
                } else {
                    $movieJob = $this->jobActor;
                }
                foreach ($jobs as $text) {
                    $job = $this->repoJob->findOneBy(array('job' => $text));
                    if (empty($job)) {
                        $job = new Job();
                        $job->setJob($text);
                        $this->em->persist($job);
                        $this->em->flush();
                    }
                    $person->addJob($job);
                }
                $this->em->persist($person);
                $this->em->flush();
                // We have  movie and person now
                $ref = $this->repoRef->findOneBy(array('movie' => $movie, 'person' => $person, 'job' => $movieJob));
                if (empty($ref)) {
                    $ref = new PersonMovieRef();
                    $ref->setMovie($movie);
                    $ref->setPerson($person);
                    $ref->setJob($movieJob);
                    $ref->setRole($actor['credit']);
                    $this->em->persist($ref);
                    $this->em->flush();
                }
            }
            $cast = $page->getDirectedBy();
            $this->addJobCast($movie, $this->jobDirector, $cast);
            $cast = $page->getWritingCredits();
            $this->addJobCast($movie, $this->jobWriter, $cast);
            $cast = $page->getProducedBy();
            $this->addJobCast($movie, $this->jobProducer, $cast);
            $cast = $page->getCinematographyBy();
            $this->addJobCast($movie, $this->jobCinematographer, $cast);
            $cast = $page->getMusicBy();
            $this->addJobCast($movie, $this->jobComposer, $cast);
            $cast = $page->getEditingBy();
            $this->addJobCast($movie, $this->jobEditor, $cast);
            $this->addOtherCast($movie, $page);
            return $movie->getId();
        }
    }

    /**
     * @param AppBundle\Entity\Movie $movie
     * @param AppBundle\Util\Imdb\ImdbMovieCastPage $cast
     * @return boolean
     */
    private function addOtherCast(\AppBundle\Entity\Movie $movie, \AppBundle\Util\Imdb\ImdbMovieCastPage $page)
    {
        $cast = array();
        $cast+= $page->getAssistantDirector();
        $cast+= $page->getCameraDepartment();
        $cast+= $page->getCameraman();
        $cast+= $page->getCastingBy();
        $this->addJobCast($movie, $this->jobOther, $cast);
        return true;
    }

    private function addJobCast(\AppBundle\Entity\Movie $movie, \AppBundle\Entity\Job $job, $cast)
    {
        if (!empty($cast) && is_array($cast)) {
            foreach ($cast as $item) {
                $person = $this->updatePerson($item['imdbId']);
                $ref = $this->repoRef->findOneBy(array('movie' => $movie, 'person' => $person, 'job' => $job));
                if (empty($ref)) {
                    $ref = new PersonMovieRef();
                    $ref->setMovie($movie);
                    $ref->setPerson($person);
                    $ref->setJob($job);
                    $ref->setRole($item['credit']);
                } else {
                    $credit = array();
                    $credit[] = $ref->getRole();
                    $credit[] = $item['credit'];
                    $ref->setRole(implode(', ', $credit));
                }
                $this->em->persist($ref);
                $this->em->flush();
            }
        }
        return true;
    }

    /**
     * @param number $imdbId
     */
    public function updatePerson($imdbId)
    {
        $person = $this->checkPersonByImdb($imdbId);
        $person->clearJobs();
        $jobs = $this->imdbPerson->getJobs();
        foreach ($jobs as $text) {
            $job = $this->repoJob->findOneBy(array('job' => $text));
            if (empty($job)) {
                $job = new Job();
                $job->setJob($text);
                $this->em->persist($job);
                $this->em->flush();
            }
            $person->addJob($job);
        }
        $this->em->persist($person);
        $this->em->flush();
        return $person;
    }

    private function checkPersonByImdb($imdbId)
    {
        $personImdb = $this->repoImdb->findOneBy(array('imdbId' => $imdbId));
        $this->imdbPerson = new ImdbPersonPage();
        $this->imdbPerson->setId($imdbId);
        if (empty($personImdb)) {
            $person = new Person();
            $person->setName($this->imdbPerson->getName());
            $person->setBirthAt($this->imdbPerson->getDob());
            $person->setDeathAt($this->imdbPerson->getDod());
            $person->setPhoto($this->imdbPerson->getPhoto());
        
            $this->em->persist($person);
            $this->em->flush();
            $personImdb = new \AppBundle\Entity\ImdbPerson();
            $personImdb->setPerson($person);
            $personImdb->setImdbId($imdbId);
            $this->em->persist($personImdb);
        } else {
            $person = $personImdb->getPerson();
            $person->setName($this->imdbPerson->getName());
            $person->setBirthAt($this->imdbPerson->getDob());
            $person->setDeathAt($this->imdbPerson->getDod());
            $person->setPhoto($this->imdbPerson->getPhoto());
        }
        return $person;
    }
}