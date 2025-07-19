<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Spatie\Permission\Traits\HasRoles;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\UserActivityLog;
use App\Models\Traits\Queryable;
use App\Models\Traits\HasRules;
use App\Models\Traits\ActiveToggle;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory,
        Notifiable,
        HasRoles,
        UserActivityLog,
        ActiveToggle,
        HasRules,
        SoftDeletes,
        Queryable;

    protected $guarded = ['id'];
    protected $guard_name = "api";

    protected $casts = [
        'is_active' => 'integer',
    ];

    const RULES = [
        'name' => ['required', 'string', 'max:255'],
        'role' => ['required', 'string', 'exists:roles,name'],
        'username' => ['required', 'string', 'max:255', 'lowercase', 'unique' => true],
        'password' => ['nullable', 'string', 'min:8'],
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
        'roles'
    ];

    protected $appends = [
        'access_permissions',
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAccessPermissionsAttribute()
    {
        return $this->getAllPermissions();
    }

    public function getRoleAttribute()
    {
        return $this->roles()->orderBy('added_at', 'desc')->first();
    }

    public function role()
    {
        return $this->roles();
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isOwner()
    {
        return $this->hasRole('owner');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
