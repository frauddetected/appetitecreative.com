<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Models\Module;
use App\Models\QR;

class Project extends Model
{
    use LogsActivity;

    protected $table = 'projects';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function modules()
    {
        return $this->hasMany(Modules::class);
    }

    public function analytics()
    {
        return $this->hasOne(Analytics::class, 'project_id');
    }

    public function sharing()
    {
        return $this->hasOne(Sharing::class, 'project_id');
    }

    public function sources()
    {
        return $this->belongsToMany(Source::class)->withPivot('count','is_active');
    }

    public function logs()
    {
        return $this->hasMany(LogAction::class);
    }

    public function i18n()
    {
        return $this->hasOne(I18N::class, 'project_id');
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function qr()
    {
        return $this->hasMany(QR::class);
    }

    public function quiz()
    {
        return $this->hasMany(Quiz::class);
    }

    public function quizResults()
    {
        return $this->hasMany(QuizResult::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function leaderboard()
    {
        return $this->hasMany(Leaderboard::class);
    }

    public function qr_params()
    {
        return $this->hasMany(QRParam::class, 'project_id');
    }

    public function live_project()
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    public function test_project()
    {
        return $this->hasOne(Project::class, 'parent_id');
    }

    public function hasModule(string $name){
        
        $module = Module::where('name', $name)->whereProjectId($this->id)->first();

        if($module && $module->is_active == true){
            return $module;
        }

        return false;

    }
}