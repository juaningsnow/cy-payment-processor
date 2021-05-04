<?php

namespace BaseCode\Auth\Requests;

use BaseCode\Auth\Contracts\Roles;
use BaseCode\Auth\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }

    public function messages()
    {
        return [];
    }
    
    public function authorize()
    {
        return true;
    }

    public function getName()
    {
        return $this->input('name');
    }

    public function getEmail()
    {
        return $this->input('email');
    }

    public function getUsername()
    {
        return $this->input('username');
    }

    public function getRoles()
    {
        if (!$this->has('roleIds')) {
            return null;
        }
        return Role::whereIn('id', $this->input('roleIds'))->get()->all();
    }

    public function getPassword()
    {
        return $this->input('password');
    }
}
