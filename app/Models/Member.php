<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';
    protected $guarded = ['id'];

    public function getDetailsAttribute($value)
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

    public function scopeCountry($query, $value)
    {
        if($value != 'All'):
            return $query->where('details->country', $value);
        else:
            return $query;
        endif;
    }
}