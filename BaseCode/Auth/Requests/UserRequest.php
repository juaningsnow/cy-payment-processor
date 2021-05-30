<?php

namespace BaseCode\Auth\Requests;

use App\Models\Bank;
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

    public function getBank()
    {
        return Bank::find($this->input('bankId'));
    }

    public function getPassword()
    {
        return $this->input('password');
    }
}
