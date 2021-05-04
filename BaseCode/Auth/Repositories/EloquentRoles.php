<?php

namespace BaseCode\Auth\Repositories;

use BaseCode\Auth\Contracts\Role;
use BaseCode\Auth\Contracts\Roles;
use BaseCode\Common\Repositories\EloquentRepository;

class EloquentRoles extends EloquentRepository implements Roles
{
    protected $role;

    public function __construct(Role $role)
    {
        parent::__construct($role);
        $this->role = $role;
    }

    public function save(Role $role)
    {
        $role->save();
        $role->syncPermissions($role->getPermissions());
        return $role;
    }

    public function delete(Role $role)
    {
        $role->delete();
        return $role;
    }
}
