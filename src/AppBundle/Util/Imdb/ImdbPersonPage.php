<?php
namespace AppBundle\Util\Imdb;

use AppBundle\Util\Imdb\ImdbPersonPageInterface;
use AppBundle\Util\Imdb\ImdbAbstractPage;

class ImdbPersonPage extends ImdbAbstractPage implements ImdbPersonPageInterface
{
    /**
    * 
    * @var array
    */
    private $jobs = array();

    /**
    * 
    * @var \DateTime
    */
    private $dob = null;

    /**
    * @var \DateTime
    */
    private $dod = null;

    /**
     * @var string
     */
    private $photo = '';

    /**
    * (non-PHPdoc)
    * @see ImdbAbstract::getUrlByLink()
    */
    public function getUrlByLink($link)
    {
        return self::BASE_URL.self::PERSON_PREFIX.$link;
    }

    /**
    * (non-PHPdoc)
    * @see ImdbAbstract::getUrlById()
    */
    public function getUrlById($id)
    {
        return self::BASE_URL.$this->getLinkById($id);
    }

    public function getShortLink()
    {
        return sprintf('nm%07d', $this->id).'/';
    }

  /**
   * 
   * @param unknown_type $tag
   */
  public function parsePage($tag = 'div')
  {
    $this->check404Page();
    if (!$this->isValid) {
      return true;
    }
    $nodes = $this->page->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute('id');
      if ($nodeId == 'name-overview-widget') {
        $this->parseNameOverviewWidget($node);
        $this->parsePhoto($node);
      }
    }
  }

  /**
   * @param \DOMElement $block
   */
  protected function parsePhoto(\DOMElement $block, $tag = 'img')
  {
      $this->photo = '';
      $images = $block->getElementsByTagName($tag);
      foreach ($images as $image) {
          $id = $image->getAttribute('id');
          if ($id == 'name-poster') {
              $src = $image->getAttribute('src');
              if (preg_match('/^(.*)_V1_(.*)(\.jpg)$/', $src, $matches)) {
                  $this->photo = $matches[1].'_V1_.jpg';
              }
          }
      }
  }

  protected function parseName(\DOMElement $block, $tag = 'span')
  {
    parent::parseName($block, $tag);
    $name = preg_replace('/\s+/', ' ', $this->getName());
    if (empty($name)) {
      return true;
    }
    $this->name = $name;
  }

  private function check404Page($tag = 'div')
  {
    $nodes = $this->page->getElementsByTagName($tag);
    if (empty($nodes->length)) {
      $this->isValid = false;
      return true;
    }
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute('id');
      if ($nodeId == 'error') {
        $this->isValid = false;
        return true;
      }
    }
  }

  /**
   * @param DOMElement $block
   */
  private function parseNameOverviewWidget(\DOMElement $block, $tag = 'td')
  {
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute('id');
      if ($nodeId == 'overview-top') {
        $this->parseName($node);
        $blocks = $node->getElementsByTagName('div');
        foreach ($blocks as $block) {
          $blockId = $block->getAttribute('id');
          switch ($blockId) {
            case 'name-job-categories':
              $this->parseJob($block);
              break;
            case 'name-born-info':
              $this->parseDob($block);
              break;
            case 'name-death-info':
              $this->parseDod($block);
              break;
          }
        }
      }
    }
  }

  /**
   * @param DOMElement $block
   * @param string $tag
   */
  private function parseJob(\DOMElement $block, $tag = 'span')
  {
    $this->jobs = array();
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $itemProp = $node->getAttribute(self::PROPERTY_ATTR);
      if ($itemProp == 'jobTitle') {
        $this->jobs[] = trim($node->textContent);
      }
    }
  }

  /**
   * @param DOMElement $block
   * @param string $tag
   */
  private function parseDob(\DOMElement $block, $tag = 'time')
  {
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $itemProp = $node->getAttribute(self::PROPERTY_ATTR);
      if ($itemProp == 'birthDate') {
        $this->dob = $node->getAttribute('datetime');
      }
    }
    $nodes = $block->getElementsByTagName('a');
    foreach ($nodes as $node) {
      $href = $node->getAttribute('href');
      if (preg_match('/birth_place/', $href)) {
        $this->birthPlace = trim($node->textContent);
      }
    }
  }

  /**
   * @param DOMElement $block
   * @param string $tag
   */
  private function parseDod(\DOMElement $block, $tag = 'time')
  {
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $itemProp = $node->getAttribute(self::PROPERTY_ATTR);
      if ($itemProp == 'deathDate') {
        $this->dod = $node->getAttribute('datetime');
      }
    }
    $nodes = $block->getElementsByTagName('a');
    foreach ($nodes as $node) {
      $href = $node->getAttribute('href');
      if (preg_match('/death_place/', $href)) {
        $this->deathPlace = trim($node->textContent);
      }
    }
  }


    /**
    * (non-PHPdoc)
    * @see imdbPersonInterface::getTypes()
    */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * (non-PHPdoc)
     * @see \AppBundle\Util\Imdb\ImdbPersonPageInterface::getJobsString()
     */
    public function getJobsString()
    {
        return implode(', ', $this->jobs);
    }

  /**
   * (non-PHPdoc)
   * @see imdbPersonInterface::getDob()
   */
  public function getDob($format = 'Y-m-d', $imdbFormat = 'Y-m-d')
  {
    if (empty($this->dob)) {
      return null;
    }
    $date = \DateTime::createFromFormat($imdbFormat, $this->dob);
    return $date;
  }

  public function showBirthAt($format = 'Y-m-d')
  {
      $date = $this->getDob();
      if ($date instanceof \DateTime) {
          return $date->format($format);
      }
      return null;
  }

  /**
   * (non-PHPdoc)
   * @see imdbPersonInterface::getDod()
   */
  public function getDod($format = 'Y-m-d', $imdbFormat = 'Y-m-d')
  {
    if (empty($this->dod)) {
      return null;
    }
    $date = \DateTime::createFromFormat($imdbFormat, $this->dod);
    return $date;
  }

    public function showDeadAt($format = 'Y-m-d')
    {
        $date = $this->getDod();
        if ($date instanceof \DateTime) {
            return $date->format($format);
        }
        return null;
    }

  
  public function getLinkById($id)
  {
    return self::PERSON_PREFIX.sprintf('nm%07d', $id).'/';
  }

  public function getPhoto()
  {
      return $this->photo;
  }
}
