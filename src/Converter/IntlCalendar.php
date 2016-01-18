<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use DateInterval;

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
    public static function import($source)
    {
        return Calends::create($source->getTime() / 1000, 'unix');
    }

    /**
     * {@inheritdoc}
     */
    public static function convert(Calends $cal)
    {
        $source = array_pop(explode('\\', get_called_class()));

        return [
            'start'    => $source::fromDateTime(\DateTime::createFromFormat('U.u', rtrim(rtrim($cal->getDate('unix'), '0'), '.'))),
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => $source::fromDateTime(\DateTime::createFromFormat('U.u', rtrim(rtrim($cal->getEndDate('unix'), '0'), '.'))),
        ];
    }
}
