<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierResourceCollection;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Purpose;
use App\Models\Supplier;
use App\Utils\CompanyIndexFilter;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
use Illuminate\Http\Request;

class SupplierApiController extends ResourceApiController
{
    use CompanyIndexFilter;
    
    const EXPORT_FILE_NAME = 'suppliers.xlsx';

    protected $suppliers;

    public function __construct(Supplier $supplier)
    {
        $this->middleware('auth:api');
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
        $request->validate([
            'name' => 'required',
            'purposeId' => 'required',
            'paymentType' => 'required',
            'accountNumber' => 'required',
            'email' => 'required|email',
            'bankId' => 'required',
            'accountId' => 'required',
        ]);
        $bank = Bank::find($request->input('bankId'));
        $purpose = Purpose::find($request->input('purposeId'));
        $account = Account::find($request->input('accountId'));
        $supplier = new Supplier;
        $supplier->setName($request->input('name'));
        $supplier->setPurpose($purpose);
        $supplier->setEmail($request->input('email'));
        $supplier->setPaymentType($request->input('paymentType'));
        $supplier->setAccountNumber($request->input('accountNumber'));
        $supplier->setBank($bank);
        $supplier->setAccount($account);
        $supplier->setCompany($request->user()->company);
        $supplier->save();
        return $this->getResource($supplier);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'purposeId' => 'required',
            'paymentType' => 'required',
            'accountNumber' => 'required',
            'email' => 'required|email',
            'bankId' => 'required'
        ]);
        $bank = Bank::find($request->input('bankId'));
        $purpose = Purpose::find($request->input('purposeId'));
        $account = Account::find($request->input('accountId'));
        $supplier = Supplier::find($id);
        $supplier->setName($request->input('name'));
        $supplier->setPurpose($purpose);
        $supplier->setEmail($request->input('email'));
        $supplier->setPaymentType($request->input('paymentType'));
        $supplier->setAccountNumber($request->input('accountNumber'));
        $supplier->setBank($bank);
        $supplier->setAccount($account);
        $supplier->save();
        return $this->getResource($supplier);
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        if ($supplier->hasInvoices()) {
            throw new GeneralApiException("Cannot delete Supplier that has Invoice entries");
        }
        $supplier->delete();
        return response('success', 200);
    }
}
