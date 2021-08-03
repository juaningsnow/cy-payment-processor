<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceBatchRequest;
use App\Http\Resources\InvoiceBatchResource;
use App\Http\Resources\InvoiceBatchResourceCollection;
use App\Models\InvoiceBatch;
use App\Models\Supplier;
use App\Utils\BatchNumberGenerator;
use App\Utils\CompanyIndexFilter;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
use DateTime;
use Illuminate\Http\Request;

class InvoiceBatchApiController extends ResourceApiController
{
    use CompanyIndexFilter;
    
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
        if ($request->input('supplierId')) {
            $invoiceBatch->setSupplier(Supplier::find($request->input('supplierId')));
        }
        $batchNumber = BatchNumberGenerator::generate();
        $invoiceBatch->setBatchName($batchNumber);
        $invoiceBatch->setDate($request->getDate());
        $invoiceBatch->setInvoiceBatchDetails($request->getInvoiceBatchDetails());
        $invoiceBatch->setCompany($request->user()->getActiveCompany());
        $invoiceBatch->setName($request->input('name'));
        $invoiceBatch->save();
        $invoiceBatch->invoiceBatchDetails()->sync($invoiceBatch->getInvoiceBatchDetails());
        return $this->getResource($invoiceBatch);
    }

    public function update($id, InvoiceBatchRequest $request)
    {
        $invoiceBatch = InvoiceBatch::find($id);
        if ($request->input('supplierId')) {
            $invoiceBatch->setSupplier(Supplier::find($request->input('supplierId')));
        }
        $invoiceBatch->setDate($request->getDate());
        $invoiceBatch->setInvoiceBatchDetails($request->getInvoiceBatchDetails());
        $invoiceBatch->setName($request->input('name'));
        $invoiceBatch->save();
        $invoiceBatch->invoiceBatchDetails()->sync($invoiceBatch->getInvoiceBatchDetails());
        return $this->getResource($invoiceBatch);
    }

    public function addInvoices($id, InvoiceBatchRequest $request)
    {
        $newInvoices = $request->addInvoiceBatchDetails();
        $invoiceBatch = InvoiceBatch::find($id);
        $updatedInvoices = array_merge($invoiceBatch->getInvoiceBatchDetails()->all(), $newInvoices);
        $invoiceBatch->setDate($request->getDate());
        $invoiceBatch->setInvoiceBatchDetails($updatedInvoices);
        $invoiceBatch->save();
        $invoiceBatch->invoiceBatchDetails()->sync($invoiceBatch->getInvoiceBatchDetails());
        return $this->getResource($invoiceBatch);
    }

    public function cancel($id)
    {
        $invoiceBatch = InvoiceBatch::find($id);
        $invoiceBatch->setCancelled(true);
        $invoiceBatch->save();
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

    public function validateForExport(Request $request)
    {
        if (!$request->user()->getActiveCompany()->getDefaultBank()) {
            throw new GeneralApiException('User company does not have a default Bank!');
        }
        if (!$request->user()->getActiveCompany()->getDefaultBank()->account) {
            throw new GeneralApiException('Company Default Bank does not have an account yet!');
        }

        return response('success', 200);
    }
}
