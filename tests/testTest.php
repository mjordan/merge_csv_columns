<?php
require 'vendor/autoload.php';

class testTest extends PHPUnit_Framework_TestCase
{
    protected $ini;

    public function testfoo()
    {
        $this->assertEquals(200, 200);
    }
}

