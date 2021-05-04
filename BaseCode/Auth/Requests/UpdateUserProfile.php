<?php

namespace BaseCode\Auth\Requests;

use Illuminate\Validation\Rule;

class UpdateUserProfile extends UserRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->getId() == $this->input('id');
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->route('userId'), 'id')
            ]
        ];
    }
}
