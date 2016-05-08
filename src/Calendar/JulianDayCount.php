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
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix(BC::mul(BC::sub($date, 2440587.5, 18), 86400, 18));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp, $format = null)
    {
        return BC::add(BC::div(Calends::fromInternalToUnix($stamp), 86400, 18), 2440587.5, 18);
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return static::toInternal(BC::add(static::fromInternal($stamp), $offset, 18));
    }
}
