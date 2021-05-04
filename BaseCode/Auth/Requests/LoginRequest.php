<?php

namespace BaseCode\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class LoginRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function getUsername()
    {
        return $this->input('username');
    }

    public function getPassword()
    {
        return $this->input('password');
    }

    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }
}
