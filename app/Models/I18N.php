<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class I18N extends Model
{
    protected $table = 'i18n';
    protected $guarded = ['id'];

    public function getCountriesAttribute($value)
    {
        return json_decode($value);
    }

    public function getLanguagesAttribute($value)
    {
        return json_decode($value);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}