<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use DateInterval;
use Moment\Moment as Source;

/**
 * Convert between Calends and Moment objects
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Moment implements ConverterInterface
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
            'start'    => Source::createFromFormat('U.u', bcadd($cal->getDate('unix'), 0, 6)),
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => Source::createFromFormat('U.u', bcadd($cal->getEndDate('unix'), 0, 6)),
        ];
    }
}
