<?php

namespace BaseCode\Auth\Models;

use App\Models\Bank;
use BaseCode\Common\Traits\HasTimestamps;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;
    use HasTimestamps;

    protected $guard_name = 'web';

    public $incrementing = false;

    protected $table = 'users';

    protected $rolesToSet = null;

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    protected $guarded = [];

    protected $allowedSettings = [];

    protected $casts = [
        'settings' => 'array'
    ];

    public function setSettings(array $values)
    {
        $this->settings = array_merge(
            $this->settings ?? [],
            Arr::only($values, $this->allowedSettings)
        );
        return $this;
    }

    public function getSettings()
    {
        return $this->settings;
    }
    
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    public function getRoles()
    {
        if ($this->rolesToSet === null) {
            return $this->roles;
        }
        return collect($this->rolesToSet);
    }

    public function setRoles(array $roles)
    {
        $this->rolesToSet = $roles;
        return $this;
    }

    public function getRoleIds()
    {
        return $this->getRoles()->map(function ($role) {
            return $role->getId();
        })->all();
    }

    public function initId()
    {
        $this->id = (string) Str::uuid();
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($value)
    {
        $this->username = $value;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
        return $this;
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

    public function setPassword($value)
    {
        $this->password = bcrypt($value);
        return $this;
    }
}
