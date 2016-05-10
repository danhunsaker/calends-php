<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarFormat extends Model
{
    use SoftDeletes;

    protected $fillable = ['format_name', 'format_string', 'description'];

    /**
     * @codeCoverageIgnore
     */
    public function calendar()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Calendar');
    }
}
