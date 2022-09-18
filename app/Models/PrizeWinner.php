<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrizeWinner extends Model
{
    protected $table = 'prize_winners';
    protected $guarded = ['id'];

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    public function user()
    {
        return $this->belongsTo(Participant::class);
    }
}