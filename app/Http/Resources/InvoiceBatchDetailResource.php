<?php

namespace App\Http\Resources;

use App\Models\Invoice;
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
            'id' => $this->id,
            'invoiceBatchId' => $this->getInvoiceBatchId(),
            'invoiceBatch' => new InvoiceBatchResource($this->whenLoaded('invoiceBatch')),
            'invoiceId' => $this->invoice_id,
            'amount' => (float) $this->amount,
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
        ];
    }
}
