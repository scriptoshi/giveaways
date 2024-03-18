<?php

namespace App\Support;

class Utils
{
    public static $units = [
        'wei' => '1',
        'kwei' => '1000',
        'mwei' => '1000000',
        'gwei' => '1000000000',
        'szabo' => '1000000000000',
        'finney' => '1000000000000000',
        'ether' => '1000000000000000000',
        'kether' => '1000000000000000000000',
        'mether' => '1000000000000000000000000',
        'gether' => '1000000000000000000000000000',
        'tether' => '1000000000000000000000000000000',
    ];

    public static $precision = [
        'wei' => '18',
        'kwei' => '15',
        'mwei' => '12',
        'gwei' => '9',
        'szabo' => '6',
        'finney' => '3',
        'ether' => '0',
        'kether' => '-3',
        'mether' => '-6',
        'gether' => '-9',
        'tether' => '-12',
    ];


    /**
     * Convert a string to kmbt format
     * @param int|float $num
     * @param int $precision
     */
    public static function kmbt($num, $precision = 1)
    {
        if ($num < 1000) {
            $n_format = number_format($num, $precision);
            $suffix = '';
        } else if ($num < 1000000) {
            $n_format = number_format($num / 1000, $precision);
            $suffix = 'K';
        } else if ($num < 1000000000) {
            $n_format = number_format($num / 1000000, $precision);
            $suffix = 'M';
        } else if ($num < 1000000000000) {
            $n_format = number_format($num / 1000000000, $precision);
            $suffix = 'B';
        } else {
            $n_format = number_format($num / 1000000000000, $precision);
            $suffix = 'T';
        }
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
        return $n_format . $suffix;
    }

    /**
     * Shorten a string
     * @param string $string
     * @param string $separator
     * @param int $limit
     */
    public static function shorten($string, $separator = '...', $limit = 20)
    {
        if (strlen($string) <= $limit) {
            return $string;
        }
        $newString = substr($string, 0, $limit) . $separator . substr($string, -$limit);
        return $newString;
    }

    /**
     * mimic javascript toFixed function
     * @param int|float $number
     */
    public function toFixed($number, $precision = 2)
    {
        $number = number_format($number, $precision, '.', '');
        return $number;
    }

    /**
     * Mimic toWei function in web3.js
     * @param int|float $number
     * @param string|int $unit
     */
    public static function parseUnits($number, $unit = 'ether')
    {
        $units =  self::$units;
        if (is_string($unit) && !isset($units[$unit])) {
            throw new \Exception('Invalid unit provided - please specify one of the following units: ' . implode(', ', array_keys($units)));
        }
        $unit = is_string($unit) ? $units[$unit] : bcpow('10', $unit);
        $number = bcmul($number, $unit);
        return $number;
    }

    /**
     * Mimic formatUnits function in ethers.js
     * @param int|float $number
     * @param string|int $unit
     */
    public static function formatUnits($number, $unit = 'ether')
    {
        $units =  self::$units;
        if (is_string($unit) && !isset($units[$unit])) {
            throw new \Exception('Invalid unit provided - please specify one of the following units: ' . implode(', ', array_keys($units)));
        }
        $unit = is_string($unit) ? $units[$unit] : bcpow('10', $unit);
        $number = bcdiv($number, $unit, 18);
        return $number;
    }
}
