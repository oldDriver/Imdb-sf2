<?php
class MyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException     Exception
     * @expectedExceptionCode 20
     */
    public function testExceptionHasErrorcode20()
    {
        throw new Exception('Some Message', 20);
    }
}