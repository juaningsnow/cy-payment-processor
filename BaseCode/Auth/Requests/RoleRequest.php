<?php

namespace BaseCode\Auth\Requests;

use BaseCode\Auth\Contracts\Permissions;
use BaseCode\Auth\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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

    public function getPermissions()
    {
        return Permission::whereIn('id', $this->input("permissionIds"))->get()->all();
    }
}
