<?php

namespace Danhunsaker\Calends\Eloquent;

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

}
