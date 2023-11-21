<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Quiz extends Model
{    
	use LogsActivity;

    protected $table = 'quiz_questions';

	public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
	
    protected $guarded = ['id'];
	protected $appends = array('total_answers_count');
	protected $casts = [
		'tags' => 'array',
	];

	public function answers()
	{
		return $this->hasMany(QuizAnswer::class, 'question_id');
	}

	public function results()
    {
        return $this->hasMany(QuizResult::class, 'question_id');
    }

	public function answersCount(){
		return $this->answers()->sum('total_answers');
	}

    public function getTotalAnswersCountAttribute()
    {
        return $this->answersCount();  
    }
}