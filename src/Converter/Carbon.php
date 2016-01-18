<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use Carbon\Carbon as Source;
use Carbon\CarbonInterval;

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
            'duration' => CarbonInterval::seconds($cal->getDuration()),
            'end'      => Source::createFromTimestamp($cal->getEndDate('unix')),
        ];
    }
}
