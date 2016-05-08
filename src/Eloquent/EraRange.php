<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EraRange extends Model
{
    use SoftDeletes;

    public function era()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Era');
    }

}
