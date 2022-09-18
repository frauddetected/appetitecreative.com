<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    protected $table = 'participants';
    protected $guarded = ['id'];

    use SoftDeletes;

    public function getProfileAttribute($value)
    {
        return json_decode($value);
    }

    public function scopeBrand($query, $value)
    {
        if(isset($value->keyword)):
            return $query->where('source_value', $value->keyword);
        else:
            return $query;
        endif;
    }
}