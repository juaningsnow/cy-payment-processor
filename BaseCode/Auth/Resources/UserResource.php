<?php

namespace BaseCode\Auth\Resources;

use App\Http\Resources\BankResource;
use App\Http\Resources\BankResourceCollection;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserBankResource;
use App\Http\Resources\UserBankResourceCollection;
use App\Http\Resources\UserCompanyResourceCollection;
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
            'isAdmin' => (bool)$this->is_admin,
            'password' => null,
            'showUrl' => route('user_show', $this->id),
            'editUrl' => route('user_edit', $this->id),
            'userCompanies' => new UserCompanyResourceCollection($this->whenLoaded('userCompanies')),
        ];
    }
}
