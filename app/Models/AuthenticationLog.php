<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthenticationLog extends Model
{
    protected $table = 'authentication_log';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'authenticatable_id');
    }
}