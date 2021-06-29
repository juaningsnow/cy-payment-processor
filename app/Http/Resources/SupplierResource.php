<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'text' => $this->getName(),
            'email' => $this->email,
            'purposeId' => $this->purpose_id,
            'purpose' => new PurposeResource($this->whenLoaded('purpose')),
            'paymentType' => $this->getPaymentType(),
            'accountNumber' => $this->getAccountNumber(),
            'bankId' => $this->bank_id,
            'accountId' => $this->account_id,
            'account' => new AccountResource($this->whenLoaded('account')),
            'bank' => new BankResource($this->whenLoaded('bank')),
            'showUrl' => route('supplier_show', $this->id),
            'editUrl' => route('supplier_edit', $this->id),
       ];
    }
}
