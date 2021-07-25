<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyBankResource extends JsonResource
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
            'companyId' => $this->company_id,
            'bank' => new BankResource($this->whenLoaded('bank')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'accountNumber' => $this->account_number,
            'accountId' => $this->account_id,
            'default' => (bool) $this->default,
       ];
    }
}
