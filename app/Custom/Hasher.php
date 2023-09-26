<?php

namespace App\Custom;

use Hashids\Hashids;

class Hasher
{
    public static function encode(...$args)
    {
        $HasherObj = new Hashids("", 10);
        return $HasherObj->encode(...$args);
    }

    public static function decode($enc)
    {
        // Decode the value.
        $HasherObj = new Hashids("", 10);
        $decoded = $HasherObj->decode($enc);

        // Return the first item if we were able to decode it.
        if (count($decoded)) {
            return $decoded[0];
        }

        return '';
    }
}
