<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FragmentText extends Model
{
    use SoftDeletes;

    /**
     * @codeCoverageIgnore
     */
    public function fragmentFormat()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\FragmentFormat');
    }
}
