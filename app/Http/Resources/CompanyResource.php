<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'cashAccountId' => $this->cash_account_id,
            'bankAccountId' => $this->bank_account_id,
            'companyBanks' => new CompanyBankResourceCollection($this->whenLoaded('companyBanks')),
            'banks' => new BankResourceCollection($this->whenLoaded('banks')),
            'companyOwners' => new CompanyOwnerResourceCollection($this->whenLoaded('companyOwners')),
            'currencies' => new CurrencyResourceCollection($this->whenLoaded('currencies')),
            'showUrl' => route('company_show', $this->id),
            'editUrl' => route('company_edit', $this->id),
       ];
    }
}
