<?php
namespace AppBundle\Util\Imdb;

interface ImdbPageInterface
{
  /**
   * @param integer $id
   */
  public function setId($id);

  public function getId();

  public function getName();

  public function getTitle();

  public function getEpisode();

  public function isValid();

  public function getLink();
}
