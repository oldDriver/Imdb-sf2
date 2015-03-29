<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



class ImportImdbMoviesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('imdb:import:movies')
            ->setDescription('Import movies from the imdb.com')
            ->addArgument(
                'limit',
                InputArgument::OPTIONAL,
                '',
                50
            )
        ;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $repoMovie = $em->getRepository('AppBundle:Movie');
        $last = $repoMovie->lastRecord();
        $start = 1;
        if (empty($last)) {
          $start = 1;
        } else {
          $start = $last[0]['imdbId'];
          $start++;
        }
        $limit = $input->getArgument('limit');
        $end = $start + $limit;
        $this->getContainer()->get('imdb.movie')->importMovies($start, $end);
        $output->writeln('Done');
    }
}