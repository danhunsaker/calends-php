<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\BC;
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
        return Calends::toInternalFromUnix(BC::add(BC::mul(BC::sub(juliantojd($greg->format('m'), $greg->format('d'), $greg->format('Y')), 2440587, 18), 86400, 18), BC::mod($greg->getTimestamp(), 86400, 18), 18));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp)
    {
        $date = Calends::fromInternalToUnix($stamp);
        return date_create(jdtojulian(BC::add(BC::div($date, 86400, 18), 2440587.5, 18)))->format('D, d M Y') . ' ' . date_create_from_format('U.u', BC::add(0, $date, 6))->format('H:i:s.u P');
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
