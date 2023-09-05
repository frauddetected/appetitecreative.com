<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogAction extends Model
{   
    protected $table = 'log_action';
    protected $guarded = ['id'];

    use SoftDeletes;

    public function getValuesAttribute($value)
    {
        return json_decode($value);
    }

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function getUserAgentAttribute($value)
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