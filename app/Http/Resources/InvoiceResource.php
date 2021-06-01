<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'supplierId' => $this->getSupplierId(),
            'date' => $this->getDate(),
            'invoiceNumber' => $this->getInvoiceNumber(),
            'amount' => $this->getAmount(),
            'description' => $this->getDescription(),
            'status' => $this->getStatus(),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'showUrl' => route('invoice_show', $this->id),
            'editUrl' => route('invoice_edit', $this->id),
        ];
    }
}
