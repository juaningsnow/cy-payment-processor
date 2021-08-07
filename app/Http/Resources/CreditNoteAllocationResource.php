<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteAllocationResource extends JsonResource
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
            'creditNoteId' => $this->credit_note_id,
            'invoiceId' => $this->invoice_id,
            'amount' => $this->amount,
            'invoice' => new InvoiceResource($this->whenLoaded('invoice'))
        ];
    }
}
