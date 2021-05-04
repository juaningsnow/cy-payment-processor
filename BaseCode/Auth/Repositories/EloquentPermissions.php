<?php

namespace BaseCode\Auth\Repositories;

use BaseCode\Auth\Contracts\Permission;
use BaseCode\Auth\Contracts\Permissions;
use BaseCode\Common\Repositories\EloquentRepository;

class EloquentPermissions extends EloquentRepository implements Permissions
{
    protected $permission;

    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
        $this->permission = $permission;
    }

    public function save(Permission $permission)
    {
        $permission->save();
        return $permission;
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
        return $permission;
    }
}
