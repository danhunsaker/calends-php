<?php

namespace Danhunsaker\Calends;

class CalendsJulianDayCount implements CalendarDefinitionInterface
{
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix(bcmul(bcsub($date, 2440587.5), 86400));
    }

    public static function fromInternal($stamp)
    {
        return bcadd(bcdiv(Calends::fromInternalToUnix($stamp), 86400), 2440587.5);
    }
}
