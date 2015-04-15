<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Movie;
use AppBundle\Entity\Person;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use AppBundle\Form\MovieImportType;
use AppBundle\Form\PersonImportType;
use AppBundle\Form\ImdbPersonType;
use AppBundle\Entity\ImdbPerson;

use Doctrine\ORM\Query as Query;

class FrontendController extends Controller
{
    /**
    * @Route("/", name="homepage")
    * @Route("/genre/{genreId}", name="movie_genre_list")
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function indexAction($genreId = null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $source = new Entity('AppBundle:Movie');
        $tableAlias = $source->getTableAlias();
        if ($genreId) {
            $source->manipulateQuery(
                function ($query) use ($tableAlias, $genreId) {
                    $query->join($tableAlias.'.genres', 'g')
                    ->andWhere('g.id = '.$genreId);
                }
            );
        }
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->hideColumns(array('id', 'imdbId'));
        $rowView = new RowAction('movie.view', 'movie_view');
        $rowView->setRouteParametersMapping('id');
        $grid->addRowAction($rowView);
        return $grid->getGridResponse(
            'frontend/movie_list.html.twig',
            array(
                'genre' => $em->getRepository('AppBundle:Genre')->findOneBy(array('id' => $genreId))
            )
        );
    }

    /**
     * @Route("/movie/{id}", name="movie_view")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function movieViewAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $movie = $em->getRepository('AppBundle:Movie')->findOneBy(array('id' => $id));
        return $this->render('frontend/movie_view.html.twig', array('movie' => $movie));
    }

    /**
     * @Route("/genres", name="genre_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function genreListAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('AppBundle:Genre');
        return $this->render(
            'frontend/genre_list.html.twig',
            array(
                'genres' => $repo->findBy(array(), array('genre' => 'ASC'))
            )
        );
    }

    
    /**
     * @Route("/person/list/{jobId}", name="person_list")
     */
    public function personListAction($jobId = null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $source = new Entity('AppBundle:Person');
        $grid = $this->get('grid');
        $tableAlias = $source->getTableAlias();
        if ($jobId) {
            $source->manipulateQuery(
                function ($query) use ($tableAlias, $jobId) {
                    $query->join($tableAlias.'.jobs', 'j')
                    ->andWhere('j.id = '.$jobId);
                }
            );
        }
        $grid->setSource($source);
        $grid->hideColumns('id');
        $rowView = new RowAction('View', 'person_view');
        $rowView->setRouteParametersMapping('id');
        $grid->addRowAction($rowView);
        return $grid->getGridResponse(
            'frontend/person_list.html.twig',
            array(
                'job' => $em->getRepository('AppBundle:Job')->findOneBy(array('id' => $jobId))
            )
        );
    }

    /**
     * @Route("/person/{id}", name="person_view")
     */
    public function personViewAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $person = $em->getRepository('AppBundle:Person')->getDetails($id);
        return $this->render(
            'frontend/person_view.html.twig',
            array(
                'person' => $person
                
            )
        );
    }

    /**
     * @Route("/jobs", name="job_list")
     */
    public function jobListAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('AppBundle:Job');
        return $this->render(
            'frontend/job_list.html.twig',
            array(
                'jobs' => $repo->listJobsAsc()
            )
        );
    }

    /**
     * @Route("/import", name="import")
     * @Security("has_role('ROLE_USER')")
     */
    public function importAction()
    {
        $request = $this->getRequest();
        $movie = new Movie();
        $movieForm = $this->createForm(new MovieImportType(), $movie);
        $person = new ImdbPerson();
        $personForm = $this->createForm(new ImdbPersonType(), $person);
        // On submit
        $movieForm->handleRequest($request);
        if ($movieForm->isSubmitted() && $movieForm->isValid()) {
            $id = $this->get('imdb.movie')->importOneMovie($movieForm['imdbId']->getData(), false);
            return $this->redirect($this->generateUrl(
                'movie_view',
                array('id' => $id)
            ));
        }
        $personForm->handleRequest($request);
        if ($personForm->isSubmitted() && $personForm->isValid()) {
            $person = $this->get('imdb.movie')->updatePerson($personForm['imdbId']->getData());
            return $this->redirect(
                $this->generateUrl(
                    'person_view',
                    array(
                        'id' => $person->getId()
                    )
                )
            );
        }
        return $this->render(
            'frontend/import.html.twig',
            array(
                'movieForm' => $movieForm->createView(),
                'personForm' => $personForm->createView(),
            )
        );
    }

    /**
     * This is the page for best practice in the Doctrine
     * @Route("/doctrine", name="doctrine")
     */
    public function doctrineAction()
    {
        /**
         * This is not a good idea to create queries in action. For this repository is good place
         * But this action is test action
         */
        $em = $this->getDoctrine()->getEntityManager();
        /**
         * 1. Always write a DQL statement for querying your object models
         */
        // this is for initially developing
        $jobs = $em->getRepository('AppBundle:Job')->findAll();
        // this is right way
        $jobs = $em->getRepository('AppBundle:Job')->listJobs();
        /**
         * 2. Beware of lazy loading when querying entities with associations
         */
        // this is no right way to show persons with jobs (for eaxample)
        $personsBad = $em->getRepository('AppBundle:Person')->findBy(array(), null, 10, 0);
        // right way
        $qb = $em->createQueryBuilder();
        $qb->select('Person', 'Job')->from('AppBundle:Person', 'Person')->setFirstResult(0)->setMaxResults(10)->leftJoin('Person.jobs', 'Job')->groupBy('Person.id')->addGroupBy('Job.id');
        //$qb->select('Person')->from('AppBundle:Person', 'Person')->setFirstResult(0)->setMaxResults(10);
        $query = $qb->getQuery();
        
        $personsRight = $query->getResult(Query::HYDRATE_ARRAY);
        /**
         * 3. Use array hydration for read only actions
         */
        
        /**
         * 4. By default Doctrine will fetch all of the properties for a given entity
         */
        
        /**
         * 5. Use prepared statements
         */
        
        //$persons = $em->getRepository('AppBundle:Person')->testSingle();
        return $this->render(
            'frontend/doctrine.html.twig',
            array(
                'personsBad' => $personsBad,
                'personsRight' => $personsRight
            )
        );
    }
}