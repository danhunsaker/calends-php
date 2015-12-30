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
    public static function import(Source $source)
    {
        return Calends::create($source->getTimestamp(), 'unix');
    }

    /**
     * {@inheritdoc}
     */
    public static function convert(Calends $cal)
    {
        return [
            'start'    => new Source("@{$cal->getDate('unix')}"),
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => new Source("@{$cal->getEndDate('unix')}"),
        ];
    }
}
