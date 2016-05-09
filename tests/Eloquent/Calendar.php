<?php

namespace Danhunsaker\Calends\Tests\Eloquent;

use Danhunsaker\Calends\Eloquent\Calendar as RealClass;

class Calendar extends RealClass
{
    public function parseDate($date)
    {
        return parent::parseDate($date);
    }

    public function formatDate(array $units, $format)
    {
        return parent::formatDate($units, $format);
    }

    public function parseOffset($offset)
    {
        return parent::parseOffset($offset);
    }

    public function unitsToTS(array $units)
    {
        return parent::unitsToTS($units);
    }

    public function tsToUnits($seconds)
    {
        return parent::tsToUnits($seconds);
    }

    public function addUnits(array $a, array $b)
    {
        return parent::addUnits($a, $b);
    }
}
