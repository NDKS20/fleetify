<?php

namespace App\Models;

use App\Models\Traits\HasRules;
use App\Models\Traits\Queryable;
use App\Models\Traits\UserActivityLog;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use UserActivityLog, Queryable, HasRules;

    protected $hidden = ['guard_name', 'pivot', 'permissions'];
    protected $appends = [
        'access_permissions'
    ];

    const RULES = [
        'title' => ['required', 'string', 'max:255'],
        'access_permissions' => ['required', 'array'],
        'access_permissions.*' => ['string', 'max:255']
    ];

    public function getAccessPermissionsAttribute()
    {
        return $this->getAllPermissions();
    }
}
