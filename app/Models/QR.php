<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QR extends Model
{
    protected $table = 'qrcodes';
    protected $guarded = ['id'];

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function addScan()
    {
        $this->scans = $this->scans + 1;
        $this->save();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}