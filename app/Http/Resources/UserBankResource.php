<?php

namespace App\Http\Resources;

use BaseCode\Auth\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBankResource extends JsonResource
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
            'bankId' => $this->bank_id,
            'userId' => $this->user_id,
            'bank' => new BankResource($this->whenLoaded('bank')),
            'user' => new UserResource($this->whenLoaded('user')),
            'accountNumber' => $this->account_number,
            'default' => (bool) $this->default,
       ];
    }
}
