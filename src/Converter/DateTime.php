<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use DateInterval;
use DateTime as Source;

class DateTime implements ConverterInterface
{
    public static function import(Source $source)
    {
        return Calends::create($source->getTimestamp(), 'unix');
    }

    public static function convert(Calends $cal)
    {
        return [
            'start'    => new Source("@{$cal->getDate('unix')}"),
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => new Source("@{$cal->getEndDate('unix')}"),
        ];
    }
}
