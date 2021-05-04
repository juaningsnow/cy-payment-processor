<?php

namespace BaseCode\Auth\Requests;

use Illuminate\Validation\Rule;

class UpdateUser extends UserRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('client_update_sttngs user');
    }

    public function rules()
    {
        return [
            'name' => "required",
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->route('userId'), 'id')
            ],
            'roleIds' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'roleIds.required' => 'Role is required',
        ];
    }
}
