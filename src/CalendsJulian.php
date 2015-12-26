<?php

namespace Danhunsaker\Calends;

use DateTime;
use IntlCalendar;
use IntlDateFormatter;

class CalendsJulian implements CalendarDefinitionInterface
{
    public static function toInternal($date)
    {
        $greg = new DateTime($date);
        return Calends::toInternalFromUnix(bcadd(bcmul(bcsub(juliantojd($greg->format('m'), $greg->format('d'), $greg->format('Y')), 2440587), 86400), bcmod($greg->getTimestamp(), 86400)));
    }

    public static function fromInternal($stamp)
    {
        $date = Calends::fromInternalToUnix($stamp);
        return jdtojulian(bcadd(bcdiv($date, 86400), 2440587.5)) . date(' H:i:s \G\M\TP', bcmod($date, 86400));
    }
}
