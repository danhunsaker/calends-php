<?php

namespace Danhunsaker\Calends;

class CalendsGregorian implements CalendarDefinitionInterface
{
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix(strtotime($date));
    }

    public static function fromInternal($stamp)
    {
        return strftime('%c', Calends::fromInternalToUnix($stamp));
    }
}
