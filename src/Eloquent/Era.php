<?php

namespace Danhunsaker\Calends\Eloquent;

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
}
