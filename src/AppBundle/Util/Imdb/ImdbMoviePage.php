<?php
namespace AppBundle\Util\Imdb;

use AppBundle\Util\Imdb\ImdbAbstractPage;
use AppBundle\Entity\Movie;
use AppBundle\Util\Imdb\ImdbPageInterface;

class ImdbMoviePage extends ImdbAbstractPage implements ImdbPageInterface
{
    /**
    * @var string
    */
    protected $language;

    /**
    * @var string
    */
    protected $duration;

    /**
    * @var string
    */
    protected $genre;

    /**
    * @var string
    */
    protected $description;

    /**
    * @var array
    */
    protected $director = array();

    /**
    * @var array
    */
    protected $writer = array();

    /**
    * @var integer
    */
    protected $year = '';

    /**
    * @var string
    */
    protected $release;

    /**
    * @var string
    */
    protected $production;

    /**
    * @var string
    */
    protected $country;

    /**
    * @var array
    */
    protected $stars = array();

    /**
    * @var array
    */
    protected $cast = array();

    /**
    * @var DateTime
    */
    protected $releaseAt;

    /**
    * @var string
    */
    protected $sound = '';

    /**
    * @var string
    */
    protected $color = '';

    /**
    * @var integer
    */
    protected $type = Movie::TYPE_CINEMA;

    /**
    * @var integer
    */
    protected $format = Movie::FORMAT_MOVIE;

    /**
     * @var string
     */
    protected $poster;

    /**
    * (non-PHPdoc)
    * @see ImdbAbstract::getUrlByLink()
    */
    public function getUrlByLink($link)
    {
        return self::BASE_URL.self::MOVIE_PREFIX.$link;
    }

    /**
    * (non-PHPdoc)
    * @see ImdbAbstract::getUrlById()
    */
    public function getUrlById($id)
    {
        return self::BASE_URL.$this->getLinkById($id);
    }

    /**
    * (non-PHPdoc)
    * @see ImdbAbstract::getLinkById()
    */
    public function getLinkById($id)
    {
        return self::MOVIE_PREFIX.sprintf('tt%07d', $id).'/';
    }

    protected function clearCredentials()
    {
        $this->year = '';
        $this->director = array();
        parent::clearCredentials();
    }

    public function parsePage($tag = 'div')
    {
        $this->format = Movie::FORMAT_MOVIE;
        $this->parseTitle();
        $nodes = $this->page->getElementsByTagName($tag);
        $this->isValid = false;
        foreach ($nodes as $node) {
            $nodeId = $node->getAttribute('id');
            switch ($nodeId) {
                case 'title-overview-widget':
                  $this->parseName($node);
                  $this->parseDescription($node);
                  $this->parseInfo($node);
                  $this->parseDirector($node);
                  $this->parsePoster($node);
                  $this->isValid = true;
                  break;
                case 'titleCast':
                  $this->parseCast($node);
                  break;
                case 'titleDetails':
                  $this->parseDetails($node);
                  break;
            }
        }
    }

    public function getRealImdbId()
    {
        $nodes = $this->page->getElementsByTagName('link');
        foreach ($nodes as $node) {
            $rel = $node->getAttribute('rel');
            if ($rel == 'canonical') {
                $href = $node->getAttribute('href');
                preg_match('/(\d+)/', $href, $matches);
                return intval($matches[0]);
            }
        }
    }


    
  private function parseTitle($tag = 'title')
  {
    
    $this->name = array();
    $nodes = $this->page->getElementsByTagName($tag);
    $title = $nodes->item(0); // there is only one title on the page
    if (empty($title)) {
      return true;
    }
    if (preg_match('/(.*)\(([a-zA-Z\s]+)(\s\d+)?\)(\s*)- IMDb/', $title->textContent, $matches)) {
      $this->name[] = $matches[1];
      $this->year = $matches[3];
      if (!empty($matches[2])) {
        $this->setFormat(trim($matches[2]), $title->textContent);
      }
    } elseif (preg_match('/(.*)\(([^\)]*)(\d{4})(.*)\)(\s*)- IMDb/', $title->textContent, $matches)) {
      $this->name[] = $matches[1];
      $this->year = $matches[3];
      if (!empty($matches[2])) {
        $this->setFormat(trim($matches[2]), $title->textContent);
      }
    } elseif (preg_match('/(.*)\s\((\d{4})\) - IMDb/', $title->textContent, $matches)) {
        // nothing to do
    } else {
        
    }
  }

  private function setFormat($format, $title)
  {
    switch ($format) {
      case 'TV Episode':
        $this->format = Movie::FORMAT_EPISODE;
        $this->type = Movie::TYPE_TELEVISION;
        break;
      case 'TV Mini-Series':
        $this->format = Movie::FORMAT_MINI_SERIES;
        $this->type = Movie::TYPE_TELEVISION;
        break;
      case 'TV Movie':
        $this->format = Movie::FORMAT_MOVIE;
        $this->type = Movie::TYPE_TELEVISION;
        break;
      case 'TV Series':
        $this->isMovie = false;
        $this->format = Movie::FORMAT_SERIES;
        $this->type = Movie::TYPE_TELEVISION;
        break;
      case 'Video Game':
        $this->format = Movie::FORMAT_VIDEO_GAME;
        $this->type = Movie::TYPE_COMMERCIAL;
        break;
      case 'Video':
        $this->type = Movie::TYPE_VIDEO;
        break;
      default:
        vfMailer::getInstance()->sendServiceEmail(
        'New movie format"',
        $title
        );
        break;
    }
  }

  public function parsePoster(\DOMElement $block, $tag = 'img')
  {
    $images = $block->getElementsByTagName($tag);
    foreach ($images as $image) {
      $imageProp = $image->getAttribute(self::PROPERTY_ATTR);
      if ($imageProp == 'image') {
        $src = $image->getAttribute('src');
        if (preg_match('/^(.*)_V1_(.*)(\.jpg)$/', $src, $matches)) {
          $this->poster = $matches[1].'_V1_.jpg';
        }
      }
    }
  }

  public function getPoster()
  {
    return $this->poster;
  }

  private function parseInfo(\DOMElement $block, $tag = 'span')
  {
    $genre = array();
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
      if ($nodeId == 'genre') {
        if ('Short' == $node->textContent) {
          $this->format = Movie::FORMAT_SHORT;
        } else {
          $genre[] = $node->textContent;
        }
      }
    }
    $this->genre = $genre;//implode(', ', $genre);
    $nodes = $block->getElementsByTagName('time');
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
      if ($nodeId == 'duration') {
        $this->duration = $node->getAttribute('datetime');
      }
    }
    $nodes = $block->getElementsByTagName('meta');
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
      if ($nodeId == 'datePublished') {
        $this->releaseAt = $node->getAttribute('content');
      }
    }
    
  }

  private function parseDescription(\DOMElement $block, $tag = 'p')
  {
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
      if ($nodeId == 'description') {
        $this->description = trim($node->textContent);
        return true;
      }
    }
  }

  private function parseDirector(\DOMElement $block, $tag = 'div')
  {
    $nodes = $block->getElementsByTagName($tag);
    foreach ($nodes as $node) {
      $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
      switch ($nodeId) {
        case 'director':
          $item = array('id' => '', 'name' => '');
          $elements = $node->getElementsByTagName('a');
          $element = $elements->item(0);
          $href = $element->getAttribute('href');
          if (preg_match('/\/name\/nm(\d+)/', $href, $matches)) {
            $item['id'] = $matches[1];
          }
          $subNodes = $node->getElementsByTagName('span');
          foreach ($subNodes as $subNode) {
            $subNodeId = $subNode->getAttribute(self::PROPERTY_ATTR);
            if ($subNodeId == 'name') {
              $item['name'] = $subNode->textContent;
              $this->director[] = $item;
            }
          }
          break;
        case 'creator':
          $subNodes = $node->getElementsByTagName('span');
          foreach ($subNodes as $subNode) {
            $subNodeId = $subNode->getAttribute(self::PROPERTY_ATTR);
            if ($subNodeId == 'name') {
              $this->writer[] = $subNode->textContent;
            }
          }
          break;
        case 'actors':
          $subNodes = $node->getElementsByTagName('span');
          foreach ($subNodes as $subNode) {
            $subNodeId = $subNode->getAttribute(self::PROPERTY_ATTR);
            if ($subNodeId == 'name') {
              $this->stars[] = $subNode->textContent;
            }
          }
          break;
      }
    }
  }

  protected function parseCast(\DOMElement $block, $tag = 'tr')
  {
    $rows = $block->getElementsByTagName($tag);
    foreach ($rows as $row) {
      $nodes = $row->getElementsByTagName('td');
      $actor = array('id' => 0, 'name' => '', 'role' => '');
      foreach ($nodes as $node) {
        $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
        if ($nodeId == 'actor') {
          $subNodes = $node->getElementsByTagName('a');
          foreach ($subNodes as $subNode) {
            $subNodeId = $subNode->getAttribute(self::PROPERTY_ATTR);
            if ($subNodeId == 'url') {
              $url = $subNode->getAttribute('href');
              preg_match('/\/name\/nm(\d+)\/?(.+)/', $url, $id);
              $actor['id'] = $id[1];
            }
          }
          $subNodes = $node->getElementsByTagName('span');
          foreach ($subNodes as $subNode) {
            $subNodeId = $subNode->getAttribute(self::PROPERTY_ATTR);
            if ($subNodeId == 'name') {
              $actor['name'] = $subNode->textContent;
            }
          }
        }
        $nodeId = $node->getAttribute('class');
        if ($nodeId == 'character') {
          $div = $node->getElementsByTagName('div');
          $actor['role'] = trim(preg_replace('/\W+/', ' ', $node->textContent));
        }
      }
      if (!empty($actor['name'])) {
        $this->cast[] = $actor;
      }
    }
  }

  private function parseDetails(\DOMElement $block, $tag = 'div')
  {
    $rows = $block->getElementsByTagName($tag);
    foreach ($rows as $row) {
      $class = $row->getAttribute('class');
      if ($class == 'txt-block') {
        $title = $row->getElementsByTagName('h4')->item(0);
        $item = $title ? $title->textContent : null;
        switch ($item) {
          case 'Country:':
            $nodes = $row->getElementsByTagName('a');
            foreach ($nodes as $node) {
              $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
              if ($nodeId == 'url') {
                $this->country = $node->textContent;
              }
            }
            break;
          case 'Production Co:':
            $nodes = $row->getElementsByTagName('span');
            foreach ($nodes as $node) {
              $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
              if ($nodeId == 'name') {
                $this->production = $node->textContent;
              }
            }
            break;
          case 'Sound Mix:':
            $nodes = $row->getElementsByTagName('a');
            foreach ($nodes as $node) {
              $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
              if ($nodeId == 'url') {
                $this->sound = trim($node->textContent);
              }
            }
            break;
          case 'Color:':
            $nodes = $row->getElementsByTagName('a');
            foreach ($nodes as $node) {
              $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
              if ($nodeId == 'url') {
                $this->color = trim($node->textContent);
              }
            }
            break;
          case 'Language:':
            $nodes = $row->getElementsByTagName('a');
            foreach ($nodes as $node) {
              $nodeId = $node->getAttribute(self::PROPERTY_ATTR);
              if ($nodeId == 'url') {
                $this->language = trim($node->textContent);
              }
            }
            break;
        }
      }
    }
  }

  protected function parseName(\DOMElement $block, $tag = '')
  {
    $nodes = $block->getElementsByTagName('h2');
    foreach ($nodes as $node) {
      $nodeClass = $node->getAttribute('class');
      if ($nodeClass == 'tv_header') {
        $this->type = Movie::TYPE_TELEVISION;
        $elements = $node->getElementsByTagName('a');
        foreach ($elements as $element) {
          $href = $element->getAttribute('href');
          if (preg_match('/\/title\/tt(\d+)\//', $href, $matches) && $this->format == Movie::FORMAT_EPISODE) {
            $this->title = trim($element->textContent);
          }
        }
      }
    }
    $nodes = $block->getElementsByTagName('h1');
    foreach ($nodes as $node) {
      $class = $node->getAttribute('class');
      if ($class == 'header') {
        $spans = $node->getElementsByTagName('span');
        foreach ($spans as $span) {
          $itemprop = $span->getAttribute('itemprop');
          $class = $span->getAttribute('class');
          if ($itemprop == 'name' && $class == 'itemprop') {
            if ($this->format == Movie::FORMAT_EPISODE) {
              $this->episode = $span->textContent;
            } else {
              $this->title = $span->textContent;
            }
          }
          if ($itemprop == 'name' && $class == 'title-extra') {
            if ($this->format == Movie::FORMAT_EPISODE) {
              $this->originalTitle = $span->textContent;
            }
          }
        }
        
      }
    }
  }

  public function parseBlock($blockName = 'CastingBy', $blockId = 'fullcredits_content')
  {
    $cast = $this->page->getElementById($blockId);
    if (empty($cast)) {
      return array();
    }
    $headers = $cast->getElementsByTagName('h4');
    $index = 0;
    foreach ($headers as $header) {
      if ($header instanceof \DOMElement) {
        $spans = $header->getElementsByTagName('span');
        foreach ($spans as $span) {
          $childs = $header->removeChild($span);
        }
      }
      $text = trim($header->textContent);
      $text = preg_replace('/\W/', '', $text);
      if ($text == $blockName) {
        break;
      }
      $index++;
    }
    return $this->parseCastTableByIndex($index, $blockId);
  }  

//   /**
//    *
//    * @param integer $index
//    * @return multitype:|multitype:multitype:string NULL unknown
//    */
//   protected function parseCastTableByIndex($index, $blockId = 'fullcredits_content')
//   {
//     $result = array();
//     $cast = $this->page->getElementById($blockId);
//     $tables = $cast->getElementsByTagName('table');
//     $table = $tables->item($index);
//     if (empty($table)) {
//       return $result;
//     }
//     $rows = $table->getElementsByTagName('tr');
//     foreach ($rows as $row) {
//       $items = $row->getElementsByTagName('td');
//       $person = array('name' => '', 'imdbId' => null, 'credit' => '');
//       foreach ($items as $td) {
//         $colspan = $td->getAttribute('colspan');
//         if (!empty($colspan) && $colspan == 3) {
//           continue 2;
//         }
//         $tdClass = $td->getAttribute('class');
//         if ($tdClass == 'name') {
//           $elements = $td->getElementsByTagName('a');
//           foreach ($elements as $element) {
//             $href = $element->getAttribute('href');
//             if (preg_match('/\/name\/nm(\d+)\/?(.+)/', $href, $matches)) {
//               $person['imdbId'] = $matches[1];
//             }
//           }
//           $person['name'] = trim($td->textContent);
//         }
//         if ($tdClass == 'credit') {
//           $person['credit'] = trim($td->textContent);
//         }
//       }
//       $result[] = $person;
//     }
//     return $result;
//   }  

  /**
   * (non-PHPdoc)
   * @see ImdbMovieInterface::getDuration()
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMovieInterface::getYear()
   */
  public function getYear()
  {
    return $this->year;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMovieInterface::getGenre()
   */
  public function getGenre()
  {
    return $this->genre;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMovieInterface::getDescription()
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMovieInterface::getDirector()
   */
  public function getDirector()
  {
    $result = array();
    foreach ($this->director as $item) {
      $result[] = $item['name'];
    }
    return implode(', ', $result);
  }

  public function getDirectorArray()
  {
    return $this->director;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMoviePageInterface::getLanguage()
   */
  public function getLanguage()
  {
    return $this->language;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMovieInterface::getWriter()
   */
  public function getWriter()
  {
    return implode(', ', $this->writer);
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMoviePageInterface::getProduction()
   */
  public function getProduction()
  {
    return $this->production;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMoviePageInterface::getCountry()
   */
  public function getCountry()
  {
    return $this->country;
  }

  public function getStars()
  {
    return implode(', ', $this->stars);
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMoviePageInterface::getCast()
   */
  public function getCast()
  {
    $result = array();
    foreach ($this->cast as $actor) {
      $result[] = $actor['name'].'-'.$actor['role'];
    }
    return implode(', ', $result);
  }

  public function getCastArray()
  {
    return $this->cast;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMoviePageInterface::getReleaseAt()
   */
  public function getReleaseAt()
  {
    return $this->releaseAt;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMoviePageInterface::getColor()
   */
  public function getColor()
  {
    return $this->color;
  }

  /**
   * (non-PHPdoc)
   * @see ImdbMoviePageInterface::getSound()
   */
  public function getSound()
  {
    return $this->sound;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getFormat()
  {
    return $this->format;
  }

  public function getMovie()
  {
    return $this->movie;
  }
}
