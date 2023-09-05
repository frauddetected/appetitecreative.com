<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $guarded = ['id'];
    protected $casts = [
        'tags' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}