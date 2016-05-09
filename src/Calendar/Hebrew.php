<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calends;
use DateTime;

/**
 * Handle operations for the Hebrew calendar system
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Hebrew implements DefinitionInterface
{
    /**
     * @var string[] $months Array of month numbers to names
     */
    protected static $months = [
        '01' => 'Tishrei',
        '02' => 'Marcheshvan',
        '03' => 'Kislev',
        '04' => 'Tebeth',
        '05' => 'Shevat',
        '6L' => 'Adar I',
        '7L' => 'Adar II',
        '06' => 'Adar',
        '07' => 'Adar',
        '08' => 'Nissan',
        '09' => 'Iyar',
        '10' => 'Siwan',
        '11' => 'Tamuz',
        '12' => 'Ab',
        '13' => 'Elul',
    ];

    /**
     * {@inheritdoc}
     */
    public static function toInternal($date, $format = null)
    {
        $greg = new DateTime(str_replace(['6L', '7L'], ['06', '07'], str_ireplace(array_values(static::$months), array_keys(static::$months), $date)));
        return Calends::toInternalFromUnix(BC::add(BC::mul(BC::sub(\JewishToJD($greg->format('n'), $greg->format('j'), $greg->format('Y')), 2440587, 18), 86400, 18), BC::mod($greg->getTimestamp(), 86400, 18), 18));
    }

    /**
     * {@inheritdoc}
     */
    public static function fromInternal($stamp, $format = null)
    {
        $date            = Calends::fromInternalToUnix($stamp);
        list($m, $d, $y) = explode('/', \jdtojewish(BC::add(BC::div($date, 86400, 18), 2440587.5, 0)));
        if (\JewishToJD(6, 1, $y) == \JewishToJD(7, 1, $y)) {
            $m = ($m == 6 ? '6L' : ($m == 7 ? '7L' : $m));
        }
        return "{$d} " . static::$months[str_pad($m, 2, '0', STR_PAD_LEFT)] . " {$y} " . date_create_from_format('U.u', BC::add(BC::mod($date, 86400, 18), 0, 6))->format('H:i:s.u P');
    }

    /**
     * {@inheritdoc}
     */
    public static function offset($stamp, $offset)
    {
        $date = date_create_from_format('U.u', BC::add(Calends::fromInternalToUnix($stamp), 0, 6))->modify($offset);
        return Calends::toInternalFromUnix($date->getTimestamp());
    }
}
