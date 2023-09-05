<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlphaNumCode extends Model
{
    protected $table = 'alphanumcodes';
    protected $guarded = ['id'];
}