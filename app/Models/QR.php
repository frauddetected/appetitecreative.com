<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class QR extends Model
{
    use LogsActivity;

    protected $table = 'qrcodes';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

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