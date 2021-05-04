<?php

namespace BaseCode\Auth\Requests;

class UpdateUserPassword extends UserRequest
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
            'password' => 'required|confirmed'
        ];
    }
}
