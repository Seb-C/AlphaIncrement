<?php

use PHPUnit\Framework\TestCase;
use AlphaIncrement\AlphaIncrement;

class AlphaIncrementTest extends TestCase
{
    public function testExpectedLengthInCustomBase()
    {
        $ai = new AlphaIncrement(2, 'AB1', false);
        $this->assertEquals(1, $ai->getExpectedLengthInCustomBase(0));
        $this->assertEquals(1, $ai->getExpectedLengthInCustomBase(1));
        $this->assertEquals(2, $ai->getExpectedLengthInCustomBase(3));
        $this->assertEquals(2, $ai->getExpectedLengthInCustomBase(5));
        $this->assertEquals(3, $ai->getExpectedLengthInCustomBase(10));
        $this->assertEquals(3, $ai->getExpectedLengthInCustomBase(11));
        $this->assertEquals(4, $ai->getExpectedLengthInCustomBase(28));
    }

    public function testConvertPositiveIntegerToCustomBase()
    {
        $ai = new AlphaIncrement(2, 'AB1', false);
        $this->assertEquals([0],          $ai->convertPositiveIntegerToCustomBase(0));
        $this->assertEquals([1],          $ai->convertPositiveIntegerToCustomBase(1));
        $this->assertEquals([1, 0],       $ai->convertPositiveIntegerToCustomBase(3));
        $this->assertEquals([1, 2],       $ai->convertPositiveIntegerToCustomBase(5));
        $this->assertEquals([1, 0, 1],    $ai->convertPositiveIntegerToCustomBase(10));
        $this->assertEquals([1, 0, 2],    $ai->convertPositiveIntegerToCustomBase(11));
        $this->assertEquals([1, 1, 1],    $ai->convertPositiveIntegerToCustomBase(13));
        $this->assertEquals([1, 0, 0, 1], $ai->convertPositiveIntegerToCustomBase(28));
    }

    public function testShuffleAlphabet()
    {
        $alphabet = 'ABCDEF';
        $ai = new AlphaIncrement();
        $shuffled = $ai->shuffleAlphabet($alphabet);

        $this->assertNotEquals($shuffled, $alphabet);
        $this->assertEquals(strlen($alphabet), strlen($shuffled));
        for ($i = 0; $i < strlen($alphabet); $i++) {
            $this->assertTrue(strpos($shuffled, $alphabet[$i]) !== false, 'Suffled alphabet doesn`t contain character `'.$alphabet[$i].'`.');
        }
    }

    public function testCharIndexesToString()
    {
        $ai = new AlphaIncrement(2, 'AB1', false);
        $this->assertEquals('A',    $ai->charIndexesToString([0]));
        $this->assertEquals('B',    $ai->charIndexesToString([1]));
        $this->assertEquals('1',    $ai->charIndexesToString([2]));
        $this->assertEquals('AB1',  $ai->charIndexesToString([0, 1, 2]));
        $this->assertEquals('11AB', $ai->charIndexesToString([2, 2, 0, 1]));
        $this->assertEquals('A1BB', $ai->charIndexesToString([0, 2, 1, 1]));
    }

    public function testPrefixWithCharacterRepresentingZero()
    {
        $ai = new AlphaIncrement(2, 'AB1', false);
        $this->assertEquals([0, 0], $ai->prefixWithCharacterRepresentingZero([]));
        $this->assertEquals([0, 1], $ai->prefixWithCharacterRepresentingZero([1]));
        $this->assertEquals([0, 2], $ai->prefixWithCharacterRepresentingZero([2]));
        $this->assertEquals([1, 2], $ai->prefixWithCharacterRepresentingZero([1, 2]));
    }

    public function testGetCaesarCipherSwitchAmounts()
    {
        $alphabet = 'ABCDEF';
        $ai = new AlphaIncrement(5, $alphabet, false);
        $sequence5 = $ai->getCaesarCipherSwitchAmounts(5);
        $sequence10 = $ai->getCaesarCipherSwitchAmounts(10);

        $this->assertCount(5, $sequence5);
        $this->assertCount(10, $sequence10);
        $this->assertSame($sequence5, array_slice($sequence10, -5));
        foreach ($sequence10 as $index) {
            $this->assertTrue(is_int($index));
        }
    }

    public function testApplyVigenereCipher()
    {
        $alphabet = 'ABCDEF';
        $ai = new AlphaIncrement(5, $alphabet, false);

        $result = $ai->applyVigenereCipher([0, 0, 5, 1, 3]);
        $this->assertCount(5, $result);
        foreach ($result as $index) {
            $this->assertLessThan(strlen($alphabet), $index);
            $this->assertTrue($index >= 0);
        }
    }

    public function testEncode()
    {
        $length = 2;
        $alphabet = 'AB1';
        $ai = new AlphaIncrement($length, $alphabet, false);

        $possibleCombinationsBeforeOverflow = strlen($alphabet) ** $length;
        $existingAlphaIds = [];
        for ($numericId = 0; $numericId < $possibleCombinationsBeforeOverflow * 3; $numericId++) {
            $alphaId = $ai->encode($numericId);
            if ($numericId < $possibleCombinationsBeforeOverflow) {
                // Testing the cases where the id is too long to fit into the specified length when converted as a string.
                // In that case, it is expected to overflow the specified limit.
                $this->assertEquals(strlen($alphaId), $length, "The alpha id `$alphaId` doesn't match the expected length ($length).");
            }
            $this->assertRegExp("/^[$alphabet]+$/", $alphaId, "The alpha id `$alphaId` doesn't match the expected alphabet ($alphabet).");
            $this->assertFalse(in_array($alphaId, $existingAlphaIds), "The alpha id `$alphaId` has been generated multiple times from a different numeric id ($numericId).");
            $existingAlphaIds[] = $alphaId;
        }
    }
}
