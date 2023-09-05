<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leaderboard extends Model
{
    protected $table = 'leaderboard';
    protected $guarded = ['id'];

    use SoftDeletes;

    public function current(){
        return $this->where('project_id', current_project()->id);
    }

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function source()
    {
        return $this->belongsTo(QR::class, 'source_value', 'keyword');
    }

    public function user()
    {
        return $this->belongsTo(Participant::class);
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class);
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