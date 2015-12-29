<?php

namespace Danhunsaker\Calends\Calendar;

use Danhunsaker\Calends\Calends;

class Unix implements DefinitionInterface
{
    public static function toInternal($date)
    {
        return Calends::toInternalFromUnix($date);
    }

    public static function fromInternal($stamp)
    {
        return Calends::fromInternalToUnix($stamp);
    }

    public static function offset($stamp, $offset)
    {
        return static::toInternal(bcadd(static::fromInternal($stamp), $offset));
    }
}
