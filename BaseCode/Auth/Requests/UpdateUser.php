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
        return true;
    }

    public function rules()
    {
        return [
            'name' => "required",
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->route('id'), 'id')
            ],
            'username' => 'required',
            'bankId' => 'required'
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
