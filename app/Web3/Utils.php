<?php

namespace App\Web3;

use App\Enums\DepositStatus;
use App\Models\Account;
use BCMathExtended\BC;
use Web3\Utils as Web3Utils;

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

    /**
     * toBn
     * Change number or number string to bignumber.
     * 
     * @param BigNumber|string|int $number
     * @return array|\phpseclib\Math\BigInteger
     */
    public static function toBn($number)
    {
        if ($number instanceof BigNumber) {
            $bn = $number;
        } elseif (is_int($number)) {
            $bn = new BigNumber($number);
        } elseif (is_numeric($number)) {
            $number = (string) $number;

            if (self::isNegative($number)) {
                $count = 1;
                $number = str_replace('-', '', $number, $count);
                $negative1 = new BigNumber(-1);
            }
            if (strpos($number, '.') > 0) {
                $comps = explode('.', $number);

                if (count($comps) > 2) {
                    throw new InvalidArgumentException('toBn number must be a valid number.');
                }
                $whole = $comps[0];
                $fraction = $comps[1];

                return [
                    new BigNumber($whole),
                    new BigNumber($fraction),
                    strlen($comps[1]),
                    isset($negative1) ? $negative1 : false
                ];
            } else {
                $bn = new BigNumber($number);
            }
            if (isset($negative1)) {
                $bn = $bn->multiply($negative1);
            }
        } elseif (is_string($number)) {
            $number = mb_strtolower($number);

            if (self::isNegative($number)) {
                $count = 1;
                $number = str_replace('-', '', $number, $count);
                $negative1 = new BigNumber(-1);
            }
            if (self::isZeroPrefixed($number) || preg_match('/^[0-9a-f]+$/i', $number) === 1) {
                $number = self::stripZero($number);
                $bn = new BigNumber($number, 16);
            } elseif (empty($number)) {
                $bn = new BigNumber(0);
            } else {
                throw new InvalidArgumentException('toBn number must be valid hex string.');
            }
            if (isset($negative1)) {
                $bn = $bn->multiply($negative1);
            }
        } else {
            throw new InvalidArgumentException('toBn number must be BigNumber, string or int.');
        }
        return $bn;
    }

}
