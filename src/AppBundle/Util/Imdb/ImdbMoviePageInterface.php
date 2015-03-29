<?php
namespace AppBundle\Util\Imdb;
interface ImdbMoviePageInterface
{
  public function getCast();

  public function getColor();

  public function getCountry();

  public function getDescription();

  public function getDirector();

  public function getDuration();

  public function getGenre();

  public function getLanguage();

  public function getProduction();

  public function getReleaseAt();

  public function getSound();

  public function getStars();

  public function getWriter();

  public function getYear();
}
