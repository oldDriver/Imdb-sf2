<?php
namespace AppBundle\Util\Imdb;

use AppBundle\Util\Imdb\PageInterface;

abstract class ImdbAbstractPage implements ImdbPageInterface
{
  /**
   * @var string
   */
  const BASE_URL = 'http://www.imdb.com';
  
  /**
   * @var string
   */
  const PERSON_PREFIX = '/name/';

  /**
   * @var string
   */
  const MOVIE_PREFIX = '/title/';

  /**
   * @var string
   */
  const MOVIE_SUFFIX = 'fullcredits';

  /**
   * @var string
   */
  const PROPERTY_ATTR = 'itemprop';

  /**
   * @var DOMDocument
   */
  protected $page = null;

  /**
   * @var integer
   */
  protected $id = 0;

  /**
   * @var string
   */
  protected $name = '';

  /**
   * @var string
   */
  protected $title = '';

  /**
   * @var string
   */
  protected $episode = '';

  /**
   * @var string
   */
  protected $originalTitle = '';

  /**
   * @var string
   */
  protected $link = '';

  /**
   * @var boolean
   */
  protected $isValid = true;

  /**
   * @var boolean
   */
  protected $isMovie = true;
  

  /**
   * @param string $link
   */
  public function __construct($link = null)
  {
    $this->page = new \DOMDocument();
    if (!empty($link)) {
      $this->link = $link;
      $url = $this->getUrlByLink($link);
      $this->loadPage($url);
      $this->parsePage();
    }
  }

  /**
   * (non-PHPdoc)
   * @see ImdbAbstractInterface::setId()
   */
  public function setId($id)
  {
    $this->clearCredentials();
    $this->id = $id;
    $this->link = $this->getLinkById($id);
    $url = $this->getUrlById($id);
    $this->loadPage($url);
    $this->parsePage();
  }

  protected function clearCredentials()
  {
    $this->page = new \DOMDocument();
    $this->isMovie = true;
    $this->isValid = true;
    $this->name = '';
    $this->title = '';
    $this->episode = '';
    $this->originalTitle = '';
  }

  /**
   * (non-PHPdoc)
   * @see ImdbAbstractPageInterface::getId()
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   *
   * @param DOMElement $block
   * @param string $tag
   */
  protected function parseName(\DOMElement $block, $tag = 'span')
  {
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
      if ($nodeId == 'name') {
        $this->name = $node->textContent;
        return true;
      }
    }
  }

  /**
   * @param string $link
   */
  abstract public function getUrlByLink($link);

  /**
   * @param integer $id
   */
  abstract public function getUrlById($id);

  /**
   * @param integer $id
   */
  abstract public function getLinkById($id);

  /**
   * @param string $tag
   */
  abstract public function parsePage($tag);

  /**
   * @param string $url
   */
  protected function loadPage($url)
  {
    @$this->page->loadHTMLFile($url);
  }

  /**
   * (non-PHPdoc)
   * @see ImdbAbstractInterface::getName()
   */
  public function getName()
  {
    return $this->name;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function getEpisode()
  {
    return $this->episode;
  }

  public function getOriginalTitle()
  {
    return $this->originalTitle;
  }

  public function getFullTitle()
  {
    $title = $this->getTitle();
    if ($this->getEpisode()!=null) {
      $title .= ' - '.$this->getEpisode();
    }
    return stripslashes($title);
  }

  /**
   * (non-PHPdoc)
   * @see ImdbAbstractInterface::isValid()
   */
  public function isValid()
  {
    return $this->isValid;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbAbstractInterface::getLink()
   */
  public function getLink()
  {
    return $this->link;
  }

  public function isMovie()
  {
    return $this->isMovie;
  }
}
