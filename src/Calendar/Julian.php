<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\Calends\Calends;
use DateTime;

/**
 * Handle operations for the Julian calendar system
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Julian implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toInternal($date)
    {
        $greg = new DateTime($date);
        return Calends::toInternalFromUnix(bcadd(bcmul(bcsub(juliantojd($greg->format('m'), $greg->format('d'), $greg->format('Y')), 2440587), 86400), bcmod($greg->getTimestamp(), 86400)));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp)
    {
        $date = Calends::fromInternalToUnix($stamp);
        return date_create(jdtojulian(bcadd(bcdiv($date, 86400), 2440587.5)))->format('D, d M Y') . ' ' . date_create_from_format('U.u', bcadd(0, $date, 6))->format('H:i:s.u P');
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        $date = new DateTime(static::fromInternal($stamp));
        return static::toInternal('@' . $date->modify($offset)->getTimestamp());
    }
}
