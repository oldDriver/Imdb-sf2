<?php
namespace AppBundle\Util\Imdb;

interface ImdbPersonPageInterface
{
    /**
    * Get person types as array
    */
    public function getJobs();

    /**
    * get person's types as string
    */
    public function getJobsString();

    /**
    * get Person Date of the Birth
    */
    public function getDob();

    /**
    * Get Person Date of the Death
    */
    public function getDod();
}
