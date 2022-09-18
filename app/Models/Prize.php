<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $table = 'prizes';
    protected $guarded = ['id'];

    public function winner()
    {
        return $this->hasMany(PrizeWinner::class);
    }
}