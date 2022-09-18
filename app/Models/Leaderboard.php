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
}