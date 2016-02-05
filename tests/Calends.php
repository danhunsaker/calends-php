<?php

namespace Danhunsaker\Calends\Tests;

use Danhunsaker\Calends\Calends as RealClass;

class Calends extends RealClass
{
    public static function getCalendar($calendar)
    {
        return parent::getCalendar($calendar);
    }

    public static function getConverter($converter)
    {
        return parent::getConverter($converter);
    }

    public static function getInternalTimeAsString($time)
    {
        return parent::getInternalTimeAsString($time);
    }

    public static function getTimesByMode(RealClass $a, RealClass $b, $mode = 'start')
    {
        return parent::getTimesByMode($a, $b, $mode);
    }
}
