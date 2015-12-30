<?php

namespace Danhunsaker\Calends\Calendar;

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
        return Calends::toInternalFromUnix(strtotime($date));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp)
    {
        return strftime('%c', Calends::fromInternalToUnix($stamp));
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return Calends::toInternalFromUnix(strtotime($offset, Calends::fromInternalToUnix($stamp)));
    }
}
