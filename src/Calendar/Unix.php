<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\Calends\Calends;

/**
 * Handle operations for Unix timestamps
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Unix implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix($date);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp)
    {
        return Calends::fromInternalToUnix($stamp);
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return static::toInternal(bcadd(static::fromInternal($stamp), $offset));
    }
}
