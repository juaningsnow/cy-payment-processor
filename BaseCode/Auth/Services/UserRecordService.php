<?php

namespace BaseCode\Auth\Services;

use BaseCode\Auth\Models\User;

class UserRecordService
{
    public static function create(
        $name,
        $email,
        $password,
        array $roles
    ) {
        $user = new User;
        $user->initId();
        $user->setName($name);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setRoles($roles);
        $user->save();
        $user->syncRoles($user->getRoles());
        return $user;
    }

    public static function update(
        User $user,
        $name,
        $email,
        array $roles = null
    ) {
        $tempUser = clone $user;
        $tempUser->setName($name);
        $tempUser->setEmail($email);
        $tempUser->setUsername($email);
        if ($roles !== null) {
            $tempUser->setRoles($roles);
        }
        $tempUser->save();
        $tempUser->syncRoles($tempUser->getRoles());
        return $tempUser;
    }

    public static function updateSettings(User $user, array $settings)
    {
        $tempUser = clone $user;
        $tempUser->setSettings($settings);
        $tempUser->save();
        return $tempUser;
    }

    public static function updatePassword(User $user, $password)
    {
        $tempUser = clone $user;
        $tempUser->setPassword($password);
        $tempUser->save();
        return $tempUser;
    }

    public static function delete(User $user)
    {
        $user->delete();
        return $user;
    }
}
