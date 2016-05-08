<?php

namespace Danhunsaker\Calends\Eloquent;

use Danhunsaker\BC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Era extends Model
{
    use SoftDeletes;

    public function unit()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Unit');
    }

    public function ranges()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\EraRange');
    }

    public function formats()
    {
        return $this->morphMany('Danhunsaker\Calends\Eloquent\FragmentFormats', 'fragment');
    }

    public function getFormatArgs(array $units)
    {
        $output = $this->unit->getFormatArgs($units);
        $raw    = $output['value'];
        $range  = $this->ranges()->where(function ($query) use ($raw) {
            $query->where(function ($query) use ($raw) {
                    $query->where([
                        ['direction', 'asc'],
                        ['start_value', '<=', $raw],
                        ['end_value', '>=', $raw],
                    ]);
                })->orWhere(function ($query) use ($raw) {
                    $query->where([
                        ['direction', 'asc'],
                        ['start_value', '<=', $raw],
                    ])->whereNull('end_value');
                })->orWhere(function ($query) use ($raw) {
                    $query->where([
                        ['direction', 'desc'],
                        ['start_value', '>=', $raw],
                        ['end_value', '<=', $raw],
                    ]);
                })->orWhere(function ($query) use ($raw) {
                    $query->where([
                        ['direction', 'desc'],
                        ['start_value', '>=', $raw],
                    ])->whereNull('end_value');
                });
            })->first();

        $output['code']  = $range->range_code;
        $output['value'] = BC::parse("({$range->start_display}-{$range->start_value})" . ($range->direction == 'asc' ? '+' : '-') . $raw, null, 0);

        return $output;
    }
}
