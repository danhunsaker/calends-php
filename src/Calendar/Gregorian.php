<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\Calends\Calends;

class Gregorian implements DefinitionInterface
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
