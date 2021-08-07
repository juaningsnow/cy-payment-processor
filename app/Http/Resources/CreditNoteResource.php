<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteResource extends JsonResource
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
            'date' => $this->date->toFormattedDateString(),
            'supplierId' => $this->supplier_id,
            'currencyId' => $this->currency_id,
            'companyId' => $this->company_id,
            'status' => $this->status,
            'appliedAmount' => $this->applied_amount,
            'total' => $this->total,
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'creditNoteAllocations' => new CreditNoteAllocationResourceCollection($this->whenLoaded('creditNoteAllocations')),
            'showUrl' => $this->getShowUrl(),
            'xeroUrl' => $this->getXeroUrl(),
        ];
    }
}
