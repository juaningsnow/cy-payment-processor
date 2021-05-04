<?php

namespace BaseCode\Auth\Repositories;

use BaseCode\Auth\Contracts\User;
use BaseCode\Auth\Contracts\Users;
use BaseCode\Common\Repositories\EloquentRepository;

class EloquentUsers extends EloquentRepository implements Users
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function getUser($identifier, $field = 'username')
    {
        return $this->user->where($field, $identifier)->first();
    }

    public function save(User $user)
    {
        $user->save();
        $user->syncRoles($user->getRoles());
        return $user;
    }

    public function delete(User $user)
    {
        $user->delete();
        return $user;
    }
}
