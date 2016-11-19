<?php

namespace AlphaIncrement;

class AlphaIncrement
{
    const DEFAULT_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected $length;
    protected $alphabet;

    public function __construct($length = 5, $alphabet = self::DEFAULT_ALPHABET)
    {
        $this->length = $length;
        $this->alphabet = $alphabet;
    }

    public function encode($integerId)
    {
        assert(is_int($integerId)); // PHP 5.6 : can't type the param as int :(
        assert($integerId >= 0);

        return 'Hello World!';
    }
}
