<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function PHPSTORM_META\map;

class InvoiceBatchResource extends JsonResource
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
            'batchName' => $this->getBatchName(),
            'date' => $this->getDate()->toFormattedDateString(),
            'total' => $this->getTotal(),
            'generated' => (bool) $this->isGenerated(),
            'status' => $this->getStatus(),
            'cancelled' => $this->getCancelled(),
            'supplierId' => $this->getSupplierId(),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'invoiceBatchDetails' => new InvoiceBatchDetailResourceCollection($this->whenLoaded('invoiceBatchDetails')),
            'showUrl' => "/invoice-batches/{$this->getId()}",
            'editUrl' => "/invoice-batches/{$this->getId()}/edit"
        ];
    }
}
