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
            'total' => $this->total,
            'amountDue' => $this->amount_due,
            'amountPaid' => $this->amount_paid,
            'description' => $this->getDescription(),
            'status' => $this->getStatus(),
            'paidBy' => $this->getPaidBy(),
            'xeroUrl' => $this->getXeroUrl(),
            'currencyId' => $this->currency_id,
            'media' => new MediaResourceCollection($this->whenLoaded('media')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'attachments' => new InvoiceXeroAttachmentResourceCollection($this->whenLoaded('invoiceXeroAttachments')),
            'invoicePayments' => new InvoicePaymentResourceCollection($this->whenLoaded('invoicePayments')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'showUrl' => route('invoice_show', $this->id),
            'editUrl' => route('invoice_edit', $this->id),
        ];
    }
}
