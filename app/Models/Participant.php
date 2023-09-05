<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    protected $table = 'participants';
    protected $guarded = ['id'];

    use SoftDeletes;

    public function member()
    {
        return $this->hasOne(Member::class, 'participant_id', 'id');
    }

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

    public function scopeCountry($query, $value)
    {
        if($value != 'All'):
            return $query->where('profile->country', $value);
        else:
            return $query;
        endif;
    }
}