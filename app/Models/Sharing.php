<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sharing extends Model
{
    protected $table = 'sharing';
    protected $guarded = ['id'];

    public function getStyleAttribute($value)
    {
        return json_decode($value);
    }

    public function getMessagesAttribute($value)
    {
        return json_decode($value);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}