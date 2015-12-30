<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use Carbon\Carbon as Source;
use Carbon\CarbonInterval;

class Carbon implements ConverterInterface
{
    public static function import(Source $source)
    {
        return Calends::create($source->timestamp, 'unix');
    }

    public static function convert(Calends $cal)
    {
        return [
            'start'    => Source::createFromTimestamp($cal->getDate('unix')),
            'duration' => CarbonInterval::seconds($cal->getDuration()),
            'end'      => Source::createFromTimestamp($cal->getEndDate('unix')),
        ];
    }
}
