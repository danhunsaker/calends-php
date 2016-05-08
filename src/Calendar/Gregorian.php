<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calends;

/**
 * Handle operations for the Gregorian calendar system
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Gregorian implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix(date_create($date)->format('U.u'));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp, $format)
    {
        return date_create_from_format('U.u', BC::add(0, Calends::fromInternalToUnix($stamp), 6))->format($format ?: 'D, d M Y H:i:s.u P');
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return Calends::toInternalFromUnix(date_create_from_format('U.u', BC::add(0, Calends::fromInternalToUnix($stamp), 6))->modify($offset)->format('U.u'));
    }
}
