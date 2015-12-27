<?php

namespace Danhunsaker\Calends\Calendar;

interface DefinitionInterface
{
    public static function toInternal($date);

    public static function fromInternal($stamp);
}
