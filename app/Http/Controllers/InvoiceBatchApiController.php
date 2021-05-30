<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceBatchRequest;
use App\Http\Resources\InvoiceBatchResource;
use App\Http\Resources\InvoiceBatchResourceCollection;
use App\Models\InvoiceBatch;
use App\Utils\BatchNumberGenerator;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
use DateTime;
use Illuminate\Http\Request;

class InvoiceBatchApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'invoiceBatchs.xlsx';

    protected $invoiceBatch;

    public function __construct(InvoiceBatch $invoiceBatch)
    {
        parent::__construct($invoiceBatch);
    }

    public function getResource($item)
    {
        return new InvoiceBatchResource($item);
    }

    public function getResourceCollection($items)
    {
        return new InvoiceBatchResourceCollection($items);
    }

    public function store(InvoiceBatchRequest $request)
    {
        $invoiceBatch = new InvoiceBatch();
        $batchNumber = BatchNumberGenerator::generate();
        $invoiceBatch->setBatchName($batchNumber);
        $invoiceBatch->setDate($request->getDate());
        $invoiceBatch->setInvoiceBatchDetails($request->getInvoiceBatchDetails());
        $invoiceBatch->save();
        $invoiceBatch->invoiceBatchDetails()->sync($invoiceBatch->getInvoiceBatchDetails());
        return $this->getResource($invoiceBatch);
    }

    public function update($id, InvoiceBatchRequest $request)
    {
        $invoiceBatch = InvoiceBatch::find($id);
        $invoiceBatch->setDate($request->getDate());
        $invoiceBatch->setInvoiceBatchDetails($request->getInvoiceBatchDetails());
        $invoiceBatch->save();
        $invoiceBatch->invoiceBatchDetails()->sync($invoiceBatch->getInvoiceBatchDetails());
        return $this->getResource($invoiceBatch);
    }

    public function destroy($id)
    {
        $invoiceBatch = InvoiceBatch::find($id);
        if ($invoiceBatch->isGenerated()) {
            throw new GeneralApiException("Cannot Delete Batch that has been generated.");
        }
        $invoiceBatch->delete();
        return response('success', 200);
    }
}
