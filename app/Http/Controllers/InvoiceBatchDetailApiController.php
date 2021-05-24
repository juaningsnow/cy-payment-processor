<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceBatchDetailResource;
use App\Http\Resources\InvoiceBatchDetailResourceCollection;
use App\Models\InvoiceBatchDetail;
use BaseCode\Common\Controllers\ResourceApiController;

class InvoiceBatchDetailApiController extends ResourceApiController
{
    protected $invoiceBatchDetail;

    public function __construct(InvoiceBatchDetail $invoiceBatchDetail)
    {
        parent::__construct($invoiceBatchDetail);
    }

    public function getResource($item)
    {
        return new InvoiceBatchDetailResource($item);
    }

    public function getResourceCollection($items)
    {
        return new InvoiceBatchDetailResourceCollection($items);
    }
}
