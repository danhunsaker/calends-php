<?php

namespace Danhunsaker\Calends;

interface CalendarDefinitionInterface
{
    public static function toInternal($date);

    public static function fromInternal($stamp);
}
