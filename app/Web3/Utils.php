<?php

namespace App\Web3;


class Utils
{
    public const ADDRESS_ZERO = '0x0000000000000000000000000000000000000000';
    public static function toBTC($satoshi, $decimals)
    {
        return bcdiv((string)$satoshi, (string)pow(10, $decimals), $decimals);
    }
    public static function toSatoshi($btc, $decimals)
    {
        return bcmul((string)$btc, (string)pow(10, $decimals));
    }

    public static function hexup(string $value): string
    {
        return strlen($value) % 2 === 0 ? $value : "0{$value}";
    }
}
