<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogQuiz extends Model
{   
    protected $table = 'log_quiz';
    protected $guarded = ['id'];

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