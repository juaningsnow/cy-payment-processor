<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
use App\Http\Resources\CompanyBankResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyResourceCollection;
use App\Models\Company;
use App\Models\CompanyBank;
use App\Models\Invoice;
use App\Models\InvoiceBatch;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class CompanyApiController extends ResourceApiController
{
    const EXPORT_FILE_NAME = 'companies.xlsx';

    protected $companies;

    public function __construct(Company $company)
    {
        $this->middleware('auth:api');
        parent::__construct($company);
    }

    public function getResource($item)
    {
        return new CompanyResource($item);
    }

    public function getResourceCollection($items)
    {
        return new CompanyResourceCollection($items);
    }

    public function store(Request $request)
    {
        $company = new Company;
        $company->name = $request->input('name');
        $company->save();
        return $this->getResource($company);
    }

    public function update($id, Request $request)
    {
        $company = Company::find($id);
        $company->name = $request->input('name');
        $company->save();
        return $this->getResource($company);
    }

    public function destroy($id)
    {
        $company = Company::find($id);
        $company->delete();
        return response('success', 200);
    }

    public function addBank($id, Request $request)
    {
        $company = \DB::transaction(function () use ($id, $request) {
            $tempCompany = Company::find($id);
            $tempCompany->banks()->attach([
                $request->input('bankId') => [
                    'account_number' => $request->input("accountNumber"),
                    'xero_account_code' => $request->input("xeroAccountCode")
                ]
            ]);
            return $tempCompany;
        });

        return $this->getResource($company);
    }

    public function removeBank($id, $bankId, Request $request)
    {
        $company = \DB::transaction(function () use ($id, $bankId) {
            $tempCompany = Company::find($id);
            $tempCompany->banks()->detach($bankId);
            return $tempCompany;
        });

        return $this->getResource($company);
    }

    public function makeDefault($id, $bankId, Request $request)
    {
        $companyBank = \DB::transaction(function () use ($id, $bankId) {
            $companyBank = CompanyBank::where('company_id', $id)->where('bank_id', $bankId)->first();
            $company = Company::find($id);
            $company->companyBanks()->each(function ($companyBank) {
                $companyBank->default = false;
                $companyBank->save();
            });
            $companyBank->default = true;
            $companyBank->save();
            return $companyBank;
        });

        return new CompanyBankResource($companyBank);
    }

    public function revokeApiConnection(Request $request)
    {
        $company = auth()->user()->getActiveCompany();
        $connectionId = $company->xero_connection_id;
        $xero = resolve(XeroInterpreter::class);
        $xero->revokeConnection($connectionId);
        $company->xero_connection_id = null;
        $company->auth_event_id = null;
        $company->xero_tenant_id = null;
        $company->save();
        $this->archiveCompanyData($company->id);
        return response('success', 200);
    }

    private function archiveCompanyData($companyId)
    {
        Invoice::where('company_id', $companyId)->get()->each(function ($invoice) {
            $invoice->invoice_number = $invoice->invoice_number.'arch'.today()->format('dmYHis');
            $invoice->archived = true;
            $invoice->save();
        });

        InvoiceBatch::where('company_id', $companyId)->get()->each(function ($batch) {
            $batch->batch_name = $batch->batch_name.'arch'.today()->format('dmYHis');
            $batch->archived = true;
            $batch->save();
        });
    }

    public function updateBank($id, $bankId, Request $request)
    {
        $companyBank = \DB::transaction(function () use ($id, $bankId, $request) {
            $companyBank = CompanyBank::where('company_id', $id)->where('bank_id', $bankId)->first();
            $companyBank->account_number = $request->input('accountNumber');
            $companyBank->xero_account_code = $request->input('xeroAccountCode');
            $companyBank->save();
            return $companyBank;
        });

        return new CompanyBankResource($companyBank);
    }
}
