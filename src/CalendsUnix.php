<?php

namespace Danhunsaker\Calends;

class CalendsUnix implements CalendarDefinitionInterface
{
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix($date);
    }

    public static function fromInternal($stamp)
    {
        return Calends::fromInternalToUnix($stamp);
    }
}
