<?php

use PHPUnit\Framework\TestCase;
use AlphaIncrement\AlphaIncrement;

class AlphaIncrementTest extends TestCase
{
    public function testAlphaIncrement()
    {
        new AlphaIncrement();
        $this->assertTrue(false);
    }
}
