<?php

namespace BaseCode\Auth\Models;

use BaseCode\Common\Traits\HasTimestamps;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    use HasTimestamps;
    protected $table = 'roles';

    protected $permissionsToSet = null;

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    public function setGuardName($value)
    {
        $this->guard_name = $value;
        return $this;
    }

    public function getGuardName()
    {
        return $this->guard_name;
    }

    public function getPermissions()
    {
        if ($this->permissionsToSet !== null) {
            return collect($this->permissionsToSet);
        }
        return $this->permissions;
    }

    public function setPermissions(array $permissions)
    {
        $this->permissionsToSet = $permissions;
        return $this;
    }

    public function getPermissionIds()
    {
        return $this->getPermissions()->map(function ($permission) {
            return $permission->getId();
        })->all();
    }
}
