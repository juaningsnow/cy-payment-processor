<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceBatchDetailResource extends JsonResource
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
            'invoiceBatchId' => $this->getInvoiceBatchId(),
            'supplierId' => $this->getSupplierId(),
            'date' => $this->getDate(),
            'invoiceNumber' => $this->getInvoiceNumber(),
            'amount' => $this->getAmount(),
        ];
    }
}
