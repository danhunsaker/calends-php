<?php

namespace Danhunsaker\Calends\Eloquent;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calendar\ObjectDefinitionInterface as Definition;
use Danhunsaker\Calends\Calends;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Calendar extends Model implements Definition
{
    use SoftDeletes;

    /**
     * Relationship with the Unit model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\Unit');
    }

    /**
     * {@inheritdoc}
     */
    public function toInternal($date)
    {
        return Calends::toInternalFromUnix($this->unitsToTS($this->parseDate($date)));
    }

    /**
     * {@inheritdoc}
     */
    public function fromInternal($stamp)
    {
        return $this->formatDate($this->tsToUnits(Calends::fromInternalToUnix($stamp)));
    }

    /**
     * {@inheritdoc}
     */
    public function offset($stamp, $offset)
    {
        return Calends::toInternalFromUnix($this->unitsToTS($this->addUnits($this->tsToUnits(Calends::fromInternalToUnix($stamp)), $this->parseOffset($offset))));
    }

    /**
     * Parse date/time string into unit => value array
     *
     * @param string $date The date/time string to parse
     * @return array
     */
    protected function parseDate($date)
    {
        return $this->getEpochUnitArray(true);
    }

    /**
     * Format unit => value array into date/time string
     *
     * @param array $units A unit => value array
     * @return string
     */
    protected function formatDate(array $units)
    {
        return json_encode($units);
    }

    /**
     * Parse relative time string into unit => value array
     *
     * @param string $offset The relative time string to parse
     * @return array
     */
    protected function parseOffset($offset)
    {
        $units = [];
        preg_match_all('|(?P<value>[-+]?[0-9]+)\s*(?P<unit>\S+)|Su', $offset, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $unitMatch = $this->units()
                              ->where('singular_name', 'like', $match['unit'])
                              ->orWhere('plural_name', 'like', $match['unit'])->get();

            if ($unitMatch->count() > 0) {
                list($unitName, $unitValue) = $unitMatch->first()->reduceAuxiliary($match['value']);
                $units[$unitName] = BC::add($unitValue, Arr::get($units, $unitName, 0), 18);
            }
        }

        return $units;
    }

    /**
     * Convert unit => value array to number of seconds
     *
     * @param array $units The unit => value array to convert
     * @return float|string
     */
    protected function unitsToTS(array $units)
    {
        $unit = $this->units()->where('scale_to', 0)->first();
        return $unit->toSeconds($this->addUnits($this->getEpochUnitArray(false), $units));
    }

    /**
     * Convert number of seconds to unit => value array
     *
     * @param float|string $seconds The number of seconds to convert
     * @return array
     */
    protected function tsToUnits($seconds)
    {
        $unit = $this->units()->where('scale_to', 0)->first();
        return $this->addUnits($this->getEpochUnitArray(true), [$unit->singular_name => BC::mul($seconds, $unit->scale_inverse ? BC::div(1, $unit->scale_amount, 18) : $unit->scale_amount, 18)]);
    }

    /**
     * Combine two unit => value arrays
     *
     * Adds each unit, carrying over values too large for a given unit to the
     * next largest one.
     *
     * @param array $a A unit => value array to add, generally a date
     * @param array $b A unit => value array to add, generally an offset
     * @return array
     */
    protected function addUnits(array $a, array $b)
    {
        // Combine unit => value arrays
        $sum = [];
        foreach ($a as $unitName => $value) {
            $sum[$unitName] = BC::add($value, isset($b[$unitName]) ? $b[$unitName] : 0, 18);
        }
        foreach ($b as $unitName => $value) {
            if ( ! isset($sum[$unitName])) {
                $sum[$unitName] = BC::add($value, 0, 18);
            }
        }

        // Perform carryover
        $coreUnit = $this->units()->where('scale_to', 0)->first();
        $sum = $coreUnit->carryOver($sum);

        return $sum;
    }

    /**
     * Retrieve the calendar's date at the Unix Epoch
     *
     * @return array
     */
    protected function getEpochUnitArray($positive = true)
    {
        $unitArray = [];

        foreach($this->units()->where('is_auxiliary', 0)->get() as $unit)
        {
            $unitArray[$unit->singular_name] = $positive ? $unit->unix_epoch : BC::mul(BC::sub($unit->unix_epoch, $unit->uses_zero ? 0 : 1, 18), -1, 18);
        }

        return $unitArray;
    }
}
