<?php

namespace Danhunsaker\Calends\Converter;

use Carbon\Carbon as Source;
use Carbon\CarbonInterval;
use Danhunsaker\Calends\Calends;

/**
 * Convert between Calends and Carbon objects
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Carbon implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function import($source)
    {
        return Calends::create($source->timestamp, 'unix');
    }

    /**
     * {@inheritdoc}
     */
    public static function convert(Calends $cal)
    {
        return [
            'start'    => Source::createFromTimestamp($cal->getDate('unix')),
            'duration' => CarbonInterval::seconds($cal->getDuration(0)),
            'end'      => Source::createFromTimestamp($cal->getEndDate('unix')),
        ];
    }
}
