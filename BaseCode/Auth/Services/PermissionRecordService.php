<?php

namespace BaseCode\Auth\Services;

use BaseCode\Auth\Models\Permission;

class PermissionRecordService
{
    public static function create($name)
    {
        $permission = new Permission;
        $permission->setName($name);
        $permission->setGuardName('web');
        $permission->save();
        return $permission;
    }
}
