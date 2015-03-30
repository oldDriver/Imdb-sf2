<?php
namespace AppBundle\Util\Imdb;

use AppBundle\Entity\Movie;
use AppBundle\Util\Imdb\ImdbMoviePage;
use AppBundle\Util\Imdb\ImdbAbstractPage;
use AppBundle\Util\Imdb\ImdbPageInterface;
use AppBundle\Util\TextTools;

/**
 * Directed By
 * Writing Credits
 * Cast - not here
 * Produced by
 * Music by
 * Cinematography by
 * Film Editing by
 * Casting By
 * Production Design by
 * Costume Design by
 * Makeup Department
 * Production Management
 * Second Unit Director or Assistant Director
 * Art Department
 * Sound Department
 * Special Effects by
 * Visual Effects by
 * Stunts
 * Camera and Electrical Department
 * Casting Department
 * Editorial Department
 * Costume and Wardrobe Department
 * Music Department
 * Transportation Department
 * Other crew
 * Thanks
 */
class ImdbMovieCastPage extends ImdbMoviePage
{
    /**
    * (non-PHPdoc)
    * @see ImdbAbstract::getUrlByLink()
    */
    public function getUrlByLink($link)
    {
        return self::BASE_URL.self::MOVIE_PREFIX.$link.self::MOVIE_SUFFIX;
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
        return self::MOVIE_PREFIX.sprintf('tt%07d', $id).'/'.self::MOVIE_SUFFIX;
    }

    /**
     * (non-PHPdoc)
     * @see \AppBundle\Util\Imdb\ImdbMoviePage::parsePage()
     */
    public function parsePage($tag = 'table')
    {
        $tables = $this->page->getElementsByTagName($tag);
        foreach ($tables as $table) {
            $tableClass = $table->getAttribute('class');
            if ($tableClass == 'cast_list') {
                $this->parseCast($table);
            }
        }
    }

    public function getActors()
    {
        $result = array();
        $tables = $this->page->getElementsByTagName('table');
        foreach ($tables as $table) {
            $class = $table->getAttribute('class');
            if ($class == 'cast_list') {
                $rows = $table->getElementsByTagName('tr');
                foreach ($rows as $row) {
                    $person = array('name' => '', 'imdbId' => null, 'credit' => '');
                    $fields = $row->getElementsByTagName('td');
                    foreach ($fields as $field) {
                        // Get actor's name
                        $property = $field->getAttribute('itemprop');
                        if ($property == 'actor') {
                            $person['name'] = TextTools::cleanString($field->textContent);
                            $elements = $field->getElementsByTagName('a');
                            foreach ($elements as $element) {
                                $href = $element->getAttribute('href');
                                if (preg_match('/\/name\/nm(\d+)\/?(.+)/', $href, $matches)) {
                                    $person['imdbId'] = $matches[1];
                                }
                            }
                        }
                        // get Role
                        $class = $field->getAttribute('class');
                        if ($class == 'character') {
                            $person['credit'] = TextTools::cleanString($field->textContent);
                        }
    
                    }
                    if (!empty($person['name']) && !empty($person['imdbId'])) {
                        $result[] = $person;
                    }
                }
            }
        }
        return $result;
    }    
    
  protected function parseCastTableByIndex($index, $blockId = 'fullcredits_content')
  {
      $result = array();
      $cast = $this->page->getElementById($blockId);
      $tables = $cast->getElementsByTagName('table');
      $table = $tables->item($index);
      if (empty($table)) {
          return $result;
      }
      $rows = $table->getElementsByTagName('tr');
      foreach ($rows as $row) {
          $fields = $row->getElementsByTagName('td');
          $person = array('name' => '', 'imdbId' => null, 'credit' => '');
          foreach ($fields as $field) {
              $colspan = $field->getAttribute('colspan');
              if (!empty($colspan) && $colspan == 4) {
                  continue 2;
              }
              $class = $field->getAttribute('class');
              if ($class == 'name') {
                  $elements = $field->getElementsByTagName('a');
                  foreach ($elements as $element) {
                      $href = $element->getAttribute('href');
                      if (preg_match('/\/name\/nm(\d+)\/?(.+)/', $href, $matches)) {
                          $person['imdbId'] = $matches[1];
                      }
                  }
                  $person['name'] = TextTools::cleanString($field->textContent);
              }
              if ($class == 'credit') {
                  $person['credit'] = TextTools::cleanString($field->textContent);
              }
          }
          if (!empty($person['name']) && !empty($person['imdbId'])) {
              $result[] = $person;
          }
      }
      return $result;
  }  

    public function getDirectedBy()
    {
        return $this->parseBlock('Directedby');
    }

    public function getWritingCredits()
    {
        return $this->parseBlock('WritingCredits');
    }

    public function getProducedBy()
    {
        return $this->parseBlock('Producedby');
    }

    public function getMusicBy()
    {
        return $this->parseBlock('Musicby');
    }

    public function getCameraman()
    {
        return $this->parseBlock('Cinematographyby');
    }

    public function getCinematographyBy()
    {
        return $this->parseBlock('Cinematographyby');
    }

    public function getEditingBy()
    {
        return $this->parseBlock('FilmEditingby');
    }

    public function getCaster()
    {
        return $this->parseBlock('CastingBy');
    }

    public function getCastingBy()
    {
        return $this->parseBlock('CastingBy');
    }

    public function getProductionDesignBy()
    {
        return $this->parseBlock('ProductionDesignby');
    }

    public function getCostumeDesignBy()
    {
        return $this->parseBlock('CostumeDesignby');
    }

    public function getMakeupDepartment()
    {
        return $this->parseBlock('MakeupDepartment');
    }

    public function getProductionManagement()
    {
        return $this->parseBlock('ProductionManagement');
    }

    public function getAssistantDirector()
    {
        return $this->parseBlock('SecondUnitDirectororAssistantDirector');
    }

    public function getArtDepartment()
    {
        return $this->parseBlock('ArtDepartment');
    }

    public function getSoundDepartment()
    {
        return $this->parseBlock('SoundDepartment');
    }

    public function getSpecialEffectsBy()
    {
        return $this->parseBlock('SpecialEffectsby');
    }

    public function getVisualEffectsBy()
    {
        return $this->parseBlock('VisualEffectsby');
    }

    public function getStunts()
    {
        return $this->parseBlock('Stunts');
    }

    public function getCameraDepartment()
    {
        return $this->parseBlock('CameraandElectricalDepartment');
    }

    public function getCastingDepartment()
    {
        return $this->parseBlock('CastingDepartment');
    }

    public function getEditorialDepartment()
    {
        return $this->parseBlock('EditorialDepartment');
    }

    public function getCostumeDepartment()
    {
        return $this->parseBlock('CostumeandWardrobeDepartment');
    }

    public function getMusicDepartment()
    {
        return $this->parseBlock('MusicDepartment');
    }

    public function getTransportationDepartment()
    {
        return $this->parseBlock('TransportationDepartment');
    }

    public function getOtherCrew()
    {
        return $this->parseBlock('Othercrew');
    }

    public function getThanks()
    {
        return $this->parseBlock('Thanks');
    }
}
