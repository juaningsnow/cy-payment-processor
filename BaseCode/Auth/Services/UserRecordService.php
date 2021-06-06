<?php

namespace BaseCode\Auth\Services;

use App\Models\Bank;
use App\Models\UserBank;
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

    public static function addBank(User $user, Bank $bank, $accountNumber)
    {
        $tempUser = clone $user;
        $tempUser->banks()->attach([
            $bank->id => [
                'account_number' => $accountNumber
            ]
        ]);
        return $tempUser;
    }

    public static function removeBank(User $user, Bank $bank)
    {
        $tempUser = clone $user;
        $tempUser->banks()->detach($bank->id);
        return $tempUser;
    }

    public static function makeDefault(UserBank $userBank)
    {
        $user = $userBank->user;
        $user->userBanks()->each(function ($userBank) {
            $userBank->default = false;
            $userBank->save();
        });
        $tempUserBank = clone $userBank;
        $tempUserBank->default = true;
        $tempUserBank->save();
        return $tempUserBank;
    }

    public static function update(
        User $user,
        $name,
        $email,
        $username,
        Bank $bank
    ) {
        $tempUser = clone $user;
        $tempUser->setName($name);
        $tempUser->setEmail($email);
        $tempUser->setUsername($username);
        $tempUser->bank()->associate($bank);
        $tempUser->save();
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
