<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EraRange extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'range_code',
        'start_value',
        'end_value',
        'start_display',
        'direction',
    ];

    /**
     * @codeCoverageIgnore
     */
    public function era()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Era');
    }
}
