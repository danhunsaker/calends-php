<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calends;

/**
 * Handle operations for Julian Day Counts
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class JulianDayCount implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toInternal($date, $format = null)
    {
        if ( ! in_array(strtolower($format), ['jd', 'gjd', 'geo', 'geo-centric', 'rjd', 'reduced', 'mjd', 'modified', 'tjd', 'truncated', 'djd', 'dublin', 'j2000', 'lilian', 'rata-die', 'mars-sol'])) {
            $format = 'gjd';
        }

        if (in_array(strtolower($format), ['jd', 'gjd', 'geo', 'geo-centric'])) {
            $jdc = $date;
        } elseif (in_array(strtolower($format), ['rjd', 'reduced'])) {
            $jdc = BC::add($date, '2400000', 18);
        } elseif (in_array(strtolower($format), ['mjd', 'modified'])) {
            $jdc = BC::add($date, '2400000.5', 18);
        } elseif (in_array(strtolower($format), ['tjd', 'truncated'])) {
            $jdc = BC::add($date, '2440000.5', 18);
        } elseif (in_array(strtolower($format), ['djd', 'dublin'])) {
            $jdc = BC::add($date, '2415020', 18);
        } elseif (in_array(strtolower($format), ['j2000'])) {
            $jdc = BC::add($date, '2451545', 18);
        } elseif (in_array(strtolower($format), ['lilian'])) {
            $jdc = BC::add($date, '2299159.5', 18);
        } elseif (in_array(strtolower($format), ['rata-die'])) {
            $jdc = BC::add($date, '1721424.5', 18);
        } elseif (in_array(strtolower($format), ['mars-sol'])) {
            $jdc = BC::add(BC::mul($date, '1.02749', 18), '2405522', 18);
        }

        return Calends::toInternalFromUnix(BC::mul(BC::sub($jdc, 2440587.5, 18), 86400, 18));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp, $format = null)
    {
        if ( ! in_array(strtolower($format), ['jd', 'gjd', 'geo', 'geo-centric', 'rjd', 'reduced', 'mjd', 'modified', 'tjd', 'truncated', 'djd', 'dublin', 'j2000', 'lilian', 'rata-die', 'mars-sol'])) {
            $format = 'gjd';
        }

        $jdc = BC::add(BC::div(Calends::fromInternalToUnix($stamp), 86400, 18), 2440587.5, 18);

        if (in_array(strtolower($format), ['jd', 'gjd', 'geo', 'geo-centric'])) {
            $output = $jdc;
        } elseif (in_array(strtolower($format), ['rjd', 'reduced'])) {
            $output = BC::sub($jdc, '2400000', 18);
        } elseif (in_array(strtolower($format), ['mjd', 'modified'])) {
            $output = BC::sub($jdc, '2400000.5', 18);
        } elseif (in_array(strtolower($format), ['tjd', 'truncated'])) {
            $output = BC::intval(BC::sub($jdc, '2440000.5', 18), 0);
        } elseif (in_array(strtolower($format), ['djd', 'dublin'])) {
            $output = BC::sub($jdc, '2415020', 18);
        } elseif (in_array(strtolower($format), ['j2000'])) {
            $output = BC::sub($jdc, '2451545', 18);
        } elseif (in_array(strtolower($format), ['lilian'])) {
            $output = BC::intval(BC::sub($jdc, '2299159.5', 18), 0);
        } elseif (in_array(strtolower($format), ['rata-die'])) {
            $output = BC::intval(BC::sub($jdc, '1721424.5', 18), 0);
        } elseif (in_array(strtolower($format), ['mars-sol'])) {
            $output = BC::div(BC::sub($jdc, '2405522', 18), '1.02749', 18);
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return static::toInternal(BC::add(static::fromInternal($stamp), $offset, 18));
    }
}
