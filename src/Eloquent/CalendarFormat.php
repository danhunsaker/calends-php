<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarFormat extends Model
{
    use SoftDeletes;

    /**
     * @codeCoverageIgnore
     */
    public function calendar()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Calendar');
    }
}
