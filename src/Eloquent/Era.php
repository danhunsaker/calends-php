<?php

namespace Danhunsaker\Calends\Eloquent;

use Danhunsaker\BC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Era extends Model
{
    use SoftDeletes;

    /**
     * @codeCoverageIgnore
     */
    public function unit()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Unit');
    }

    /**
     * @codeCoverageIgnore
     */
    public function ranges()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\EraRange');
    }

    /**
     * @codeCoverageIgnore
     */
    public function formats()
    {
        return $this->morphMany('Danhunsaker\Calends\Eloquent\FragmentFormats', 'fragment');
    }

    public function getFormatArgs(array $units)
    {
        $output = $this->unit->getFormatArgs($units);
        $raw    = $output['value'];
        $range  = $this->ranges()->where(function ($query) use ($raw) {
            // @codeCoverageIgnoreStart
            $query->where(function ($query) use ($raw) {
                $query->where('direction', 'asc')
                      ->where('start_value', '<=', $raw)
                      ->where('end_value', '>=', $raw);
            })->orWhere(function ($query) use ($raw) {
                $query->where('direction', 'asc')
                      ->where('start_value', '<=', $raw)
                      ->whereNull('end_value');
            })->orWhere(function ($query) use ($raw) {
                $query->where('direction', 'desc')
                      ->where('start_value', '>=', $raw)
                      ->where('end_value', '<=', $raw);
            })->orWhere(function ($query) use ($raw) {
                $query->where('direction', 'desc')
                      ->where('start_value', '>=', $raw)
                      ->whereNull('end_value');
            });
            // @codeCoverageIgnoreEnd
        })->first();

        $output['code']  = $range->range_code;
        $output['value'] = BC::parse("({$range->start_display}" . ($range->direction == 'asc' ? '-' : '+') . "{$range->start_value})" . ($range->direction == 'asc' ? '+' : '-') . $raw, null, 0);

        return $output;
    }

    public function getEpochValue()
    {
        return $this->unit->getEpochValue();
    }

    public function unitValue(array $parsed)
    {
        $raw   = Arr::get($parsed, 'value', 0);
        $code  = Arr::get($parsed, 'code', $this->default_range);
        $range = $this->ranges()->where('range_code', $code)
            ->where(function ($query) use ($raw) {
                // @codeCoverageIgnoreStart
                $query->where(function ($query) use ($raw) {
                    $query->where('direction', 'asc')
                          ->where('start_value', '<=', "{$raw} - start_display")
                          ->where('end_value', '>=', "{$raw} - start_display");
                })->orWhere(function ($query) use ($raw) {
                    $query->where('direction', 'asc')
                          ->where('start_value', '<=', "{$raw} - start_display")
                          ->whereNull('end_value');
                })->orWhere(function ($query) use ($raw) {
                    $query->where('direction', 'desc')
                          ->where('start_value', '>=', "start_display - {$raw}")
                          ->where('end_value', '<=', "start_display - {$raw}");
                })->orWhere(function ($query) use ($raw) {
                    $query->where('direction', 'desc')
                          ->where('start_value', '>=', "start_display - {$raw}")
                          ->whereNull('end_value');
                })->orWhere(function ($query) use ($raw) {
                    $query->whereRaw('1=1');
                });
                // @codeCoverageIgnoreEnd
            })->first();
        $output = [
            $this->unit->internal_name,
            BC::parse("({$range->start_value}" . ($range->direction == 'asc' ? '-' : '+') . "{$range->start_display})" . ($range->direction == 'asc' ? '+' : '-') . $raw, null, 0)
        ];

        return $output;
    }
}
