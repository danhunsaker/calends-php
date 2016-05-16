<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\BC;
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
    public static function toInternal($date, $format = null)
    {
        $format = is_null($format) ? 18 : BC::max([BC::min([$format, 18], 0), 0], 0);
        return Calends::toInternalFromUnix(BC::round($date, $format));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp, $format = null)
    {
        $format = is_null($format) ? 18 : BC::max([BC::min([$format, 18], 0), 0], 0);
        return BC::round(Calends::fromInternalToUnix($stamp), $format);
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        return static::toInternal(BC::add(static::fromInternal($stamp), $offset, 18));
    }
}
