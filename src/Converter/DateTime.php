<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use DateInterval;
use DateTime as Source;

/**
 * Convert between Calends and DateTime objects
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class DateTime implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function import($source)
    {
        return Calends::create($source->getTimestamp(), 'unix');
    }

    /**
     * {@inheritdoc}
     */
    public static function convert(Calends $cal)
    {
        return [
            'start'    => Source::createFromFormat('U.u', rtrim($cal->getDate('unix'), '0') . '0'),
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => Source::createFromFormat('U.u', rtrim($cal->getEndDate('unix'), '0') . '0'),
        ];
    }
}
