<?php

namespace Danhunsaker\Calends\Eloquent;

use Danhunsaker\BC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Unit extends Model
{
    use SoftDeletes;

    protected $casts = [
        'scale_inverse' => 'boolean',
        'uses_zero'     => 'boolean',
        'is_auxiliary'  => 'boolean',
    ];

    /**
     * @codeCoverageIgnore
     */
    public function calendar()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Calendar');
    }

    /**
     * @codeCoverageIgnore
     */
    public function scalesToMe()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\Unit', 'scale_to');
    }

    /**
     * @codeCoverageIgnore
     */
    public function scaleMeTo()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Unit', 'scale_to');
    }

    /**
     * @codeCoverageIgnore
     */
    public function names()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\UnitName');
    }

    /**
     * @codeCoverageIgnore
     */
    public function lengths()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\UnitLength');
    }

    /**
     * @codeCoverageIgnore
     */
    public function eras()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\Era');
    }

    /**
     * @codeCoverageIgnore
     */
    public function formats()
    {
        return $this->morphMany('Danhunsaker\Calends\Eloquent\FragmentFormats', 'fragment');
    }

    public function toSeconds(array $unitArray)
    {
        $scaled = null;

        if (empty($unitArray)) {
            $scaled = 0;
        } else {
            foreach ($this->scalesToMe()->get() as $unit) {
                if ( ! $unit->is_auxiliary) {
                    $unitArray = $unit->toSeconds($unitArray);
                }
            }

            if (array_key_exists($this->internal_name, $unitArray)) {
                $myVal  = BC::sub(Arr::get($unitArray, $this->internal_name, $this->uses_zero ? 0 : 1), $this->uses_zero ? 0 : 1, 18);
                $scaled = $this->scaleReduce($myVal);

                if ($this->scale_to != 0) {
                    $unitArray[$this->scaleMeTo->internal_name] = BC::add(Arr::get($unitArray, $this->scaleMeTo->internal_name, 0), $scaled, 18);
                    unset($unitArray[$this->internal_name]);
                    $scaled = null;
                }
            }
        }

        return ( ! is_null($scaled)) ? $scaled : $unitArray;
    }

    public function reduceAuxiliary($value)
    {
        if ($this->is_auxiliary && ! is_null($this->scaleMeTo)) {
            return $this->scaleMeTo->reduceAuxiliary($this->scaleReduce($value));
        } else {
            return [$this->internal_name, $value];
        }
    }

    protected function scaleReduce($value)
    {
        if ( ! is_null($this->scale_amount)) {
            $scaled = BC::parse("{$value} " . ($this->scale_inverse ? '/' : '*') . " {$this->scale_amount}", null, 18);
        } else {
            $lengths = $this->lengths()->get();
            $lCount  = $lengths->count();
            if ($lCount == 0) {
                $scaled = 0;
            } else {
                $lSum       = $lengths->sum('scale_amount');

                $adjLoops   = BC::div($value, $lCount, 0);
                $adjUnits   = BC::modfrac($value, $lCount, 18);
                $adjRemains = BC::modfrac($adjUnits, 1, 18);
                $adjUnits   = BC::add($adjUnits, $this->getEpochValue(), 0);

                $adjustment = BC::mul($adjLoops, $lSum, 18);

                for ($lNum = $this->getEpochValue(); BC::comp($lNum, $adjUnits) < 0; $lNum = BC::parse("({$lNum} + 1) % {$lCount}", null, 0)) {
                    $adjustment = BC::add($adjustment, $lengths[$lNum]->scale_amount, 18);
                }

                $scaled = BC::parse("{$adjustment} + ({$adjRemains} * {lAmount})", ['lAmount' => $lengths[BC::parse("({$lNum} - 1 + {$lCount}) % {$lCount}", null, 0)]->scale_amount], 18);
            }
        }

        return $scaled;
    }

    public function carryOver(array $unitArray)
    {
        $myVal       = $this->is_auxiliary ? 0 : BC::sub(Arr::get($unitArray, $this->internal_name, $this->uses_zero ? 0 : 1), $this->uses_zero ? 0 : 1, 18);
        $exprInverse = '({myVal} * {scale}) %% {scale}';
        $exprNormal  = '({myVal} - ({myVal} %% {scale})) / {scale}';

        foreach ($this->scalesToMe()->get() as $unit) {
            $unitVal        = BC::sub(Arr::get($unitArray, $unit->internal_name, $unit->uses_zero ? 0 : 1), $unit->uses_zero ? 0 : 1, 18);
            $unitAdjustment = 0;
            $myAdjustment   = 0;

            if ($unit->scale_inverse) {
                $adjComp  = 1;
                $unitExpr = $exprInverse;
                $myExpr   = '{adjust} / {scale}';
            } else {
                $adjComp  = $myVal;
                $unitExpr = $exprNormal;
                $myExpr   = '{adjust} * {scale}';
            }

            if ( ! is_null($unit->scale_amount)) {
                $unitAdjustment = BC::parse($unitExpr, ['myVal' => $myVal, 'scale' => $unit->scale_amount], 18);
                $myAdjustment   = BC::parse($myExpr, ['adjust' => $unitAdjustment, 'scale' => $unit->scale_amount], 18);
            } else {
                $lengths = $unit->lengths()->get();
                $lCount  = $lengths->count();
                $lSum    = $lengths->sum('scale_amount');

                if ($lCount > 0) {
                    $unitAdjustment = BC::add($unitAdjustment, BC::parse("({$unitExpr}) * {$lCount}", ['myVal' => $myVal, 'scale' => $lSum], 18), 18);
                    $myAdjustment   = BC::add($myAdjustment, BC::parse($myExpr, ['adjust' => BC::div($unitAdjustment, $lCount, 18), 'scale' => $lSum], 18), 18);

                    for ($lNum = 0; BC::comp($adjComp, $myAdjustment) > 0 && BC::comp($myVal, $lengths[$lNum]->scale_amount) >= 0; $lNum = BC::parse("({$lNum} + 1) % {$lCount}", null, 0)) {
                        $unitAdjustment = BC::add($unitAdjustment, 1, 18);
                        $myAdjustment   = BC::add($myAdjustment, $lengths[$lNum]->scale_amount, 18);
                    }

                    if (BC::comp($adjComp, $myAdjustment) < 0) {
                        $unitAdjustment = BC::sub($unitAdjustment, 1, 18);
                        $myAdjustment   = BC::sub($myAdjustment, $lengths[BC::parse("({$lNum} - 1 + {$lCount}) % {$lCount}", null, 0)]->scale_amount, 18);
                    }
                }
            }

            $unitArray[$unit->internal_name] = BC::add(BC::add($unitVal, $unitAdjustment, 18), $unit->uses_zero ? 0 : 1, 18);

            if ( ! $unit->is_auxiliary) {
                $unitArray[$this->internal_name] = $myVal = BC::add(BC::sub($myVal, $myAdjustment, 18), $this->uses_zero ? 0 : 1, 18);
            }

            $unitArray = $unit->carryOver($unitArray);
        }

        return $unitArray;
    }

    public function getFormatArgs(array $units)
    {
        $raw = BC::add(array_key_exists($this->internal_name, $units) ? $units[$this->internal_name] : 0, 0, 0);
        if (is_null($this->scale_amount)) {
            if (with($lenObj = $this->lengths()->where('unit_value', $raw))->count() > 0) {
                $length = $lenObj->first()->scale_amount;
            } else {
                $length = 0;
            }
        } else {
            $length = $this->scale_amount;
        }

        return [
            'length' => $length,
            'value'  => $raw,
        ];
    }

    public function getEpochValue()
    {
        return $this->unix_epoch ?: 0;
    }
}
