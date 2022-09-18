<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{    
    protected $table = 'log_quiz';
    protected $guarded = ['id'];

    public function question()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answer()
    {
        return $this->belongsTo(QuizAnswer::class);
    }
}