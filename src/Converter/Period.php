<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use League\Period\Period as Source;

class Period implements ConverterInterface
{
    public static function import(Source $source)
    {
        return Calends::create([
            'start' => $source->getStartDate()->getTimestamp(),
            'end'   => $source->getEndDate()->getTimestamp()
        ], 'unix');
    }

    public static function convert(Calends $cal)
    {
        return new Source("@{$cal->getDate('unix')}", "@{$cal->getEndDate('unix')}");
    }
}
