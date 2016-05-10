<?php

namespace Danhunsaker\Calends\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitName extends Model
{
    use SoftDeletes;

    protected $fillable = ['unit_name', 'name_context'];

    /**
     * @codeCoverageIgnore
     */
    public function unit()
    {
        return $this->belongsTo('Danhunsaker\Calends\Eloquent\Unit');
    }
}
