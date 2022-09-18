<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $table = 'analytics';
    protected $guarded = ['id'];

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}