<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitName extends Model
{
    use SoftDeletes;

    public function unit()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Unit');
    }

}
