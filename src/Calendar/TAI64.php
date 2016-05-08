<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calends;

/**
 * Handle operations for TAI64NA external dates
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class TAI64 implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toInternal($date)
    {
        $date = str_pad(str_pad($date, 16, '0', STR_PAD_LEFT), 32, '0', STR_PAD_RIGHT);

        $time = [
            'seconds' => gmp_strval(gmp_init('0x' . substr($date, 0, 16), 16), 10),
            'nano'    => gmp_strval(gmp_init('0x' . substr($date, 16, 8), 16), 10),
            'atto'    => gmp_strval(gmp_init('0x' . substr($date, 24, 8), 16), 10),
        ];

        if (BC::comp($time['seconds'], BC::pow(2, 63, 18), 18) >= 0) {
            $time = [
                'seconds' => BC::sub(BC::pow(2, 63, 18), 1, 0),
                'nano'    => '999999999',
                'atto'    => '999999999',
            ];
        }

        return $time;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp, $format = null)
    {
        return str_pad(gmp_strval(gmp_init($stamp['seconds'], 10), 16), 16, '0', STR_PAD_LEFT)
             . str_pad(gmp_strval(gmp_init($stamp['nano'], 10), 16), 8, '0', STR_PAD_LEFT)
             . str_pad(gmp_strval(gmp_init($stamp['atto'], 10), 16), 8, '0', STR_PAD_LEFT);
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return Calends::toInternalFromUnix(BC::add(Calends::fromInternalToUnix($stamp), BC::add(Calends::fromInternalToUnix(static::toInternal($offset)), 0x4000000000000000, 18), 18));
    }
}
