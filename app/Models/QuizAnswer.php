<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{    
    protected $table = 'quiz_answers';
    protected $guarded = ['id'];
}