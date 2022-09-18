<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{    
    protected $table = 'quiz_questions';
    protected $guarded = ['id'];
	protected $appends = array('total_answers_count');

	public function answers()
	{
		return $this->hasMany(QuizAnswer::class, 'question_id');
	}

	public function answersCount(){
		return $this->answers()->sum('total_answers');
	}

    public function getTotalAnswersCountAttribute()
    {
        return $this->answersCount();  
    }
}