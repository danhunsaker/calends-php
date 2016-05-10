<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FragmentText extends Model
{
    use SoftDeletes;

    protected $fillable = ['fragment_value', 'fragment_text'];

    /**
     * @codeCoverageIgnore
     */
    public function fragmentFormat()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\FragmentFormat');
    }
}
