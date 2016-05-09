<?php

namespace Danhunsaker\Calends\Eloquent;

use Danhunsaker\BC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FragmentFormat extends Model
{
    use SoftDeletes;

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
    public function fragment()
    {
        return $this->morphTo();
    }

    /**
     * @codeCoverageIgnore
     */
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

    public function getParseString()
    {
        return preg_replace('/%[^$]+\\$/', '%', $this->format_string);
    }

    public function parseValue($value)
    {
        preg_match_all('/%([^$]+)\\$/', $this->format_string, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);

        $i      = count($matches[1]);
        $values = [];
        foreach (array_reverse($matches[1]) as $match) {
            $fragmentType = strtolower(ltrim(strrchr(get_class($this->fragment), '\\'), '\\')); // era or unit
            $fragmentName = $this->fragment->internal_name;
            $partBegin    = stripos($match[0], '{');
            if ($partBegin >= 0) {
                $partEnd      = stripos($match[0], '}', $partBegin);
                $fragmentPart = trim(substr($match[0], $partBegin, $partEnd), '{}');
            } else {
                $fragmentPart = 'unknown';
            }
            $type = "{$fragmentType}.{$fragmentName}.{$fragmentPart}";

            if (with($txtObj = $this->texts()->where('fragment_text', $value))->count() > 0) {
                $value = $txtObj->first()->fragment_value;
            }

            $invert = preg_replace('/##(\d+|\\{[^}]+\\})/', '*${1}+{epoch}%%${1}', str_replace(
                ['\\*', '-%', '\\', '+', '-', ':', '/', '*', ':', '%%',          '%',          '__',         '$$'       ],
                ['__',  '$$', '##', ':', '+', '-', ':', '/', '*', '+{epoch}\\*', '+{epoch}-%', '+{epoch}%%', '+{epoch}%'],
                $match[0]));
            $output = BC::parse($invert, ['epoch' => $this->fragment->getEpochValue(), $fragmentPart => $value], 0);

            $values[$i] = [$type, $output];

            $i--;
        }

        return $values;
    }
}
