<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';
    protected $guarded = ['id'];

    public function enable()
    {
        $this->is_active = true;
        $this->save();
    }

    public function disbale()
    {
        $this->is_active = false;
        $this->save();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}