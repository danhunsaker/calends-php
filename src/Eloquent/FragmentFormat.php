<?php

namespace Danhunsaker\Calends\Eloquent;

use Danhunsaker\BC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FragmentFormat extends Model
{
    use SoftDeletes;

    public function calendar()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Calendar');
    }

    public function fragment()
    {
        return $this->morphTo();
    }

    public function texts()
    {
        return $this->hasMany('Danhunsaker\Calends\Eloquent\FragmentText');
    }

    public function formatFragment(array $units)
    {
        $args = $this->fragment->getFormatArgs($units);

        $format = $this->format_string;
        preg_match_all('/%([^$]+)\\$/', $format, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);

        $i      = count($matches[1]);
        $values = [];
        foreach (array_reverse($matches[1]) as $match) {
            $values[$i] = BC::parse($match[0], $args, 0);
            $format     = substr_replace($format, $i, $match[1], strlen($match[0]));

            if (with($txtObj = $this->texts()->where('fragment_value', $values[$i]))->count() > 0) {
                $values[$i] = $txtObj->first()->fragment_text;
            }

            $i--;
        }

        return vsprintf($format, array_values($values));
    }
}
