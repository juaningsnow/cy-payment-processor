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
            'purpose' => $this->getPurpose(),
            'paymentType' => $this->getPaymentType(),
            'accountNumber' => $this->getAccountNumber(),
            'swiftCode' => $this->getSwiftCode(),
            'showUrl' => "/suppliers/{$this->getId()}",
            'editUrl' => "/suppliers/{$this->getId()}/edit"
       ];
    }
}
