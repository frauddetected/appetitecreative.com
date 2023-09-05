<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{    
    protected $table = 'quiz_answers';
    protected $guarded = ['id'];

    public function question()
    {
        return $this->belongsTo(Quiz::class, 'question_id');
    }

    public function results()
    {
        return $this->hasMany(QuizResult::class, 'answer_id');
    }
}