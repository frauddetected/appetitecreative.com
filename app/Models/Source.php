<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'sources';
    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsToMany(Project::class)->withPivot('count','is_active');
    }

    public function addCount()
    {
        $this->count = $this->count + 1;
        $this->save();
    }
}