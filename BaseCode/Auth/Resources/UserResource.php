<?php

namespace BaseCode\Auth\Resources;

use App\Http\Resources\BankResource;
use BaseCode\Auth\Resources\RoleResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'accountNumber' => $this->account_number,
            'bankId' => $this->bank_id,
            'bank' => new BankResource($this->whenLoaded('bank')),
        ];
    }
}
