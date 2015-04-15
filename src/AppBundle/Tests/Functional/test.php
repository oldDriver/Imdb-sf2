<?php
class WebTest extends PHPUnit_Extensions_Selenium2TestCase
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://sf2.imdb.local/');
    }
    
    /**
     * @test
     */
    public function testTitle()
    {
        $this->url('http://sf2.imdb.local/');
        $this->assertEquals('', $this->title());
    }

    public function testPages()
    {
        $this->url('http://sf2.imdb.local/');
        $this->click('GENRES');
    }
}
