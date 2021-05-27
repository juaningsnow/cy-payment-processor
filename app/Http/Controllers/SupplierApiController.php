<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierResourceCollection;
use App\Models\Bank;
use App\Models\Purpose;
use App\Models\Supplier;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
use Illuminate\Http\Request;

class SupplierApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'suppliers.xlsx';

    protected $suppliers;

    public function __construct(Supplier $supplier)
    {
        parent::__construct($supplier);
    }

    public function getResource($item)
    {
        return new SupplierResource($item);
    }

    public function getResourceCollection($items)
    {
        return new SupplierResourceCollection($items);
    }

    public function store(Request $request)
    {
        $bank = Bank::find($request->input('bankId'));
        $purpose = Purpose::find($request->input('purposeId'));
        $supplier = new Supplier;
        $supplier->setName($request->input('name'));
        $supplier->setPurpose($purpose);
        $supplier->setPaymentType($request->input('paymentType'));
        $supplier->setAccountNumber($request->input('accountNumber'));
        $supplier->setBank($bank);
        $supplier->save();
        return $this->getResource($supplier);
    }

    public function update($id, Request $request)
    {
        $bank = Bank::find($request->input('bankId'));
        $purpose = Purpose::find($request->input('purposeId'));
        $supplier = Supplier::find($id);
        $supplier->setName($request->input('name'));
        $supplier->setPurpose($purpose);
        $supplier->setPaymentType($request->input('paymentType'));
        $supplier->setAccountNumber($request->input('accountNumber'));
        $supplier->setBank($bank);
        $supplier->save();
        return $this->getResource($supplier);
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        if ($supplier->hasInvoiceBatchDetails()) {
            throw new GeneralApiException("Supplier is already used in an Invoice Entry");
        }
        $supplier->delete();
        return response('success', 200);
    }
}
