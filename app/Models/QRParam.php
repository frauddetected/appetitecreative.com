<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRParam extends Model
{
    protected $table = 'qrcode_params';
    protected $guarded = ['id'];
}