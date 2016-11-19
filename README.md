AlphaIncrement is a simple library that allows you to generate really unique strings from an incremental id.

This does not aim to be cryptographically secure nor to be used for security purposes.

This is useful if you need an easy way to get some kind of unique serial number without especially needing to hide the original id.

As you can see below, there is still some kind of pattern in the results that an end-user may guess, but it shouldn't be a problem in most cases.

Basic use
=========

```php
$ai = new AlphaIncrement();
echo $ai->encode(1); // prints "TKSAC"
echo $ai->encode(2); // prints "TKSAD"
echo $ai->encode(3); // prints "TKSAZ"
echo $ai->encode(42); // prints "TKSKY"
echo $ai->encode(314); // prints "TKSGB"
echo $ai->encode(7654321); // prints "9JXCC"
```

Advanced use
============

You can pass three arguments to the constructor  : 
- The length of the result string (default: 5);
- The alphabet (list of chars to use, as a string). Default: digits and uppercase letters. Unicode characters are supported;
- You can pass false here to disable the alphabet shuffling, which is used by default to improve randomization of the results.

```php
$ai = new AlphaIncrement(4, '😀😃😉😋😎😴😕😓😞😇', false);
echo $ai->encode(1); // prints "😋😞😉😴"
echo $ai->encode(2); // prints "😋😞😉😕"
echo $ai->encode(3); // prints "😋😞😉😓"
echo $ai->encode(42); // prints "😋😞😉😞"
echo $ai->encode(314); // prints "😋😞😉😇"
echo $ai->encode(654321); // prints "😋😞😉😀"
```

Big numbers
===========

Of course you can't fit an infinite amount of ids in a size limited string. The way to fallback from an id too large to fit in the specified size is to make a string larger that the expected size

```php
$ai = new AlphaIncrement(3);
echo $ai->encode(10); // returns "SAT"
echo $ai->encode(42); // returns "SKY"
echo $ai->encode(7654321); // returns "9JXCC"
echo $ai->encode(PHP_INT_MAX); // returns "1UL1RDNFPP5OW"
```

