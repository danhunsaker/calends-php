<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use DateInterval;
use IntlCalendar as Source;

/**
 * Convert between Calends and IntlCalendar objects
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class IntlCalendar implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function import(Source $source)
    {
        return Calends::create($source->getTime() / 1000, 'unix');
    }

    /**
     * {@inheritdoc}
     */
    public static function convert(Calends $cal)
    {
        return [
            'start'    => Source::fromDateTime("@{$cal->getDate('unix')}"),
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => Source::fromDateTime("@{$cal->getEndDate('unix')}"),
        ];
    }
}
