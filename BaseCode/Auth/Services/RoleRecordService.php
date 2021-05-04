<?php

namespace BaseCode\Auth\Services;

use BaseCode\Auth\Models\Role;

class RoleRecordService
{
    public static function create(
        $name,
        array $permissions
    ) {
        $role = new Role;
        $role->setName($name);
        $role->setGuardName('web');
        $role->setPermissions($permissions);
        $role->save();
        $role->syncPermissions($role->getPermissions());
        return $role;
    }

    public static function update(
        Role $role,
        $name,
        array $permissions
    ) {
        $tempRole = clone $role;
        $tempRole->setName($name);
        $tempRole->setPermissions($permissions);
        $tempRole->save();
        $tempRole->syncPermissions($tempRole->getPermissions());
        return $tempRole;
    }

    public static function delete(Role $role)
    {
        $role->delete();
        return $role;
    }
}
