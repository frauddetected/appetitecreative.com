<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Yadahan\AuthenticationLog\AuthenticationLogable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use AuthenticationLogable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'is_admin'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'admin',
        'projects_list',
        'role',
    ];

    protected $with = ['currentProject','notifications','unreadNotifications'];

    public function getAdminAttribute()
    {
        return $this->is_admin == true;
    }

    public function getRoleAttribute()
    {
        $role = project_role();
        $roles = ['superadmin','admin','editor','contributor'];
        $key = array_search($role, $roles);

        return [
            'name' => $role,
            'level' => $key
        ];
    }

    public function getProjectsAttribute()
    {
        $ownProjects = $this->ownProjects;
        $otherProjects = $this->otherProjects;

        return $ownProjects->merge($otherProjects);
    }

    public function getProjectsListAttribute()
    {
        $ownProjects = $this->ownProjects->where('is_test', false);
        $otherProjects = $this->otherProjects->where('is_test', false);

        return $ownProjects->merge($otherProjects);
    }
    
    public function ownProjects()
    {
        return $this->hasMany(Project::class);
    }

    public function otherProjects()
    {
        return $this->belongsToMany(Project::class)->withPivot('role');
    }

    public function currentProject()
    {
        return $this->belongsTo(Project::class, 'current_project_id');
    }
}
