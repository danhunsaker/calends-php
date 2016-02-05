<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use DateInterval;
use IntlTimeZone;

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
     * @var string $locale The locale to use when creating new IntlCalendar objects
     */
    public static $locale = null;

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
        $source = @array_pop(explode('\\', get_called_class()));

        $start = $source::createInstance(IntlTimeZone::getGMT(), static::$locale);
        $start->setTime(bcmul($cal->getDate('unix'), 1000, 15));

        $end   = $source::createInstance(IntlTimeZone::getGMT(), static::$locale);
        $end->setTime(bcmul($cal->getEndDate('unix'), 1000, 15));

        return [
            'start'    => $start,
            'duration' => new DateInterval("PT{$cal->getDuration()}S"),
            'end'      => $end,
        ];
    }
}
