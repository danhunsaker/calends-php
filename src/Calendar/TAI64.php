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
    public static function toInternal($date, $format = null)
    {
        if ( ! in_array(strtolower($format), ['tai64', 'tai64n', 'tai64na', 'numeric'])) {
            $format = 'tai64na';
        }

        if (substr(strtolower($format), 0, 5) == 'tai64') {
            $date = str_pad(str_pad($date, 16, '0', STR_PAD_LEFT), 32, '0', STR_PAD_RIGHT);

            $time = [
                'seconds' => gmp_strval(gmp_init('0x' . substr($date, 0, 16), 16), 10),
                'nano'    => gmp_strval(gmp_init('0x' . substr($date, 16, 8), 16), 10),
                'atto'    => gmp_strval(gmp_init('0x' . substr($date, 24, 8), 16), 10),
            ];
        } elseif (strtolower($format) == 'numeric') {
            list($time['seconds'], $frac) = explode('.', "{$date}.");

            $frac         = str_pad($frac, 18, '0', STR_PAD_RIGHT);
            $time['nano'] = substr($frac, 0, 9);
            $time['atto'] = substr($frac, 9, 9);
        }

        if (BC::comp($time['seconds'], BC::pow(2, 63, 18), 18) >= 0) {
            $time = [
                'seconds' => BC::sub(BC::pow(2, 63, 18), 1, 0),
                'nano'    => '999999999',
                'atto'    => '999999999',
            ];
        } elseif (BC::comp($time['seconds'], 0, 18) <= 0) {
            $time = [
                'seconds' => '0',
                'nano'    => '0',
                'atto'    => '0',
            ];
        }

        return $time;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp, $format = null)
    {
        if ( ! in_array(strtolower($format), ['tai64', 'tai64n', 'tai64na', 'numeric'])) {
            $format = 'tai64na';
        }

        if (substr(strtolower($format), 0, 5) == 'tai64') {
            $return = str_pad(gmp_strval(gmp_init($stamp['seconds'], 10), 16), 16, '0', STR_PAD_LEFT);
            if (substr(strtolower($format), 0, 6) == 'tai64n') {
                $return .= str_pad(gmp_strval(gmp_init($stamp['nano'], 10), 16), 8, '0', STR_PAD_LEFT);
                if (strtolower($format) == 'tai64na') {
                    $return .= str_pad(gmp_strval(gmp_init($stamp['atto'], 10), 16), 8, '0', STR_PAD_LEFT);
                }
            }
        } elseif (strtolower($format) == 'numeric') {
            $frac   = str_pad($stamp['nano'], 9, '0', STR_PAD_LEFT) . str_pad($stamp['atto'], 9, '0', STR_PAD_LEFT);
            $return = "{$stamp['seconds']}.{$frac}";
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return Calends::toInternalFromUnix(BC::add(Calends::fromInternalToUnix($stamp), BC::add(Calends::fromInternalToUnix(static::toInternal($offset)), 0x4000000000000000, 18), 18));
    }
}
