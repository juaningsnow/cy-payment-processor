<?php

namespace BaseCode\Auth\Resources;

use App\Http\Resources\BankResource;
use App\Http\Resources\BankResourceCollection;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserBankResource;
use App\Http\Resources\UserBankResourceCollection;
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
            'companyId' => $this->company_id,
            'password' => null,
            'userBanks' => new UserBankResourceCollection($this->whenLoaded('userBanks')),
            'banks' => new BankResourceCollection($this->whenLoaded('banks')),
            'company' => new CompanyResource($this->whenLoaded('company'))
        ];
    }
}
