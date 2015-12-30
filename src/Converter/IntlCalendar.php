<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use DateInterval;
use IntlCalendar as Source;

class IntlCalendar implements ConverterInterface
{
    public static function import(Source $source)
    {
        return Calends::create($source->getTime() / 1000, 'unix');
    }

    public static function convert(Calends $cal)
    {
        return [
            'start'    => Source::fromDateTime("@{$cal->getDate('unix')}"),
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => Source::fromDateTime("@{$cal->getEndDate('unix')}"),
        ];
    }
}
