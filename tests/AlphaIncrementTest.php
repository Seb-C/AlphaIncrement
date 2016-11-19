<?php

use PHPUnit\Framework\TestCase;
use AlphaIncrement\AlphaIncrement;

class AlphaIncrementTest extends TestCase
{
    protected $length;
    protected $alphabet;
    protected $alphaIncrement;

    public function __construct()
    {
        parent::__construct();
        $this->length = 2;
        $this->alphabet = 'AB1';
        $this->alphaIncrement = new AlphaIncrement($this->length, $this->alphabet);
    }

    public function testEncoding()
    {
        $possibleCombinationsBeforeOverflow = strlen($this->alphabet) ** $this->length;
        $existingAlphaIds = [];
        for ($numericId = 0; $numericId < $possibleCombinationsBeforeOverflow * 3; $numericId++) {
            $alphaId = $this->alphaIncrement->encode($numericId);
            if ($numericId < $possibleCombinationsBeforeOverflow) {
                // Testing the cases where the id is too long to fit into the specified length when converted as a string.
                // In that case, it is expected to overflow the specified limit.
                $this->assertEquals(strlen($alphaId), $this->length, "The alpha id `$alphaId` doesn't match the expected length ($this->length).");
            }
            $this->assertRegExp("/^[$this->alphabet]+$/", $alphaId, "The alpha id `$alphaId` doesn't match the expected alphabet ($this->alphabet).");
            $this->assertFalse(in_array($alphaId, $existingAlphaIds), "The alpha id `$alphaId` has been generated multiple times from a different numeric id ($numericId).");
            $existingAlphaIds[] = $alphaId;
        }
    }
}
