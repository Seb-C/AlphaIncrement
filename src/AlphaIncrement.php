<?php

namespace AlphaIncrement;

class AlphaIncrement
{
    const DEFAULT_LENGTH = 5;
    const DEFAULT_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const DEFAULT_SHUFFLE_ALPHABET = true;

    protected $length;
    protected $alphabet;

    /**
     * @param int $length Expected length of the generated string
     * @param string $alphabet Characters to use for the resulting string
     * @param boolean $shuffleAlphabet Wether or not to shuffle the given
     * alphabet to hide some pattern that may be visible.
     */
    public function __construct(
        $length          = self::DEFAULT_LENGTH,
        $alphabet        = self::DEFAULT_ALPHABET,
        $shuffleAlphabet = self::DEFAULT_SHUFFLE_ALPHABET
    ) {
        assert($length > 0);
        assert(strlen($alphabet) > 0);

        $this->length = $length;
        $this->alphabet = $alphabet;

        if ($shuffleAlphabet) {
            $this->alphabet = $this->shuffleAlphabet($this->alphabet);
        }
    }

    /**
     * Converts an auto-incremental integer to a unique string
     * @param integer $integerId The id to convert
     * @return string
     */
    public function encode($integerId)
    {
        assert(is_int($integerId)); // PHP 5.6 : can't type the param as int :(
        assert($integerId >= 0);
        
        $newId = $this->convertPositiveIntegerToCustomBase($integerId);
        $newId = $this->prefixWithCharacterRepresentingZero($newId);
        $newId = $this->applyVigenereCipher($newId);

        return $this->charIndexesToString($newId);
    }

    /**
     * Shuffles tyhe given alphabet with a pseudo randomization.
     * @param string $alphabet
     * @return string
     */
    public function shuffleAlphabet($alphabet)
    {
        mt_srand(strlen($alphabet));
        $alphabetAsArray = str_split($alphabet);
        usort($alphabetAsArray, function ($char) {
            return mt_rand(-1, 1);
        });
        return implode('', $alphabetAsArray);
    }

    /**
     * Converts the given integer to the base defined (by radix)
     * by the alphabet `$this->alphabet`.
     * @param integer $number
     * @return array(integer) The resulting string as an array of indexes in the alphabet.
     */
    public function convertPositiveIntegerToCustomBase($number)
    {
        $expectedLength = $this->getExpectedLengthInCustomBase($number);

        $digits = [];
        $remaining = $number;
        for ($i = $expectedLength - 1; $i > 0; $i--) {
            $positionValue = strlen($this->alphabet) ** $i;
            $digits[] = (int) floor($remaining / $positionValue);
            $remaining = $remaining % $positionValue;
        }
        $digits[] = $remaining;

        return $digits;
    }

    /**
     * Determines the length that will take the given number in the target base.
     * @param integer $number
     * @return integer
     */
    public function getExpectedLengthInCustomBase($number)
    {
        $expectedLength = 0;
        while(strlen($this->alphabet) ** ($expectedLength + 1) <= $number) {
            $expectedLength++;
        }
        return $expectedLength + 1;
    }

    /**
     * Converts an array of indexes to a string with the
     * characters at those indexes in the alphabet.
     * @param array(integer)
     * @return array(integer)
     */
    public function charIndexesToString(array $indexes)
    {
        $string = '';
        foreach ($indexes as $index) {
            $string .= $this->alphabet[$index];
        }
        return $string;
    }

    /**
     * Left pads the given string with the character representing
     * zero in `$this->alphabet`, to match the expected length `$this->length`.
     * @param array(integer)
     * @return array(integer)
     */
    public function prefixWithCharacterRepresentingZero(array $indexes)
    {
        while(count($indexes) < $this->length) {
            array_unshift($indexes, 0);
        }
        return $indexes;
    }

    /**
     * Generates a pseudo random integer arrays, in a way
     * that the last elements of the array are always
     * the same for a given alphabet.
     * @param integer $length
     * @return array(integer)
     */
    public function getCaesarCipherSwitchAmounts($length)
    {
        mt_srand(strlen($this->alphabet));
        $switchs = [];
        for ($i = 0; $i < $length; $i++) {
            array_unshift($switchs, mt_rand());
        }
        return $switchs;
    }

    /**
     * Applies a vigenere cipher to kind of randomize the string
     * @param array(integer)
     * @return array(integer)
     */
    public function applyVigenereCipher(array $indexes)
    {
        $switchs = $this->getCaesarCipherSwitchAmounts(count($indexes));

        $newIndexes = [];
        for ($i = 0; $i < count($indexes); $i++) {
            $newIndexes[] = ($indexes[$i] + $switchs[$i]) % strlen($this->alphabet);
        }
        return $newIndexes;
    }
}
