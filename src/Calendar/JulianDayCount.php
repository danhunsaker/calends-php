<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\Calends\Calends;

class JulianDayCount implements DefinitionInterface
{
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix(bcmul(bcsub($date, 2440587.5), 86400));
    }

    public static function fromInternal($stamp)
    {
        return bcadd(bcdiv(Calends::fromInternalToUnix($stamp), 86400), 2440587.5);
    }

    public static function offset($stamp, $offset)
    {
        return static::toInternal(bcadd(static::fromInternal($stamp), $offset));
    }
}
