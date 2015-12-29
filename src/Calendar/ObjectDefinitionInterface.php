<?php

namespace Danhunsaker\Calends\Calendar;

interface ObjectDefinitionInterface
{
    // Convert a date representation to an internal TAI array
    public function toInternal($date);

    // Convert an internal TAI array to a date representation
    public function fromInternal($stamp);

    // Calculate the TAI array at $offset from another TAI array
    public function offset($stamp, $offset);
}
