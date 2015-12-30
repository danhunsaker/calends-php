<?php

namespace Danhunsaker\Calends\Calendar;

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
        return Calends::toInternalFromUnix(bcmul(bcsub($date, 2440587.5), 86400));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp)
    {
        return bcadd(bcdiv(Calends::fromInternalToUnix($stamp), 86400), 2440587.5);
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return static::toInternal(bcadd(static::fromInternal($stamp), $offset));
    }
}
