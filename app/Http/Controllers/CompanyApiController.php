<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
use App\Http\Resources\CompanyBankResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyResourceCollection;
use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyBank;
use App\Models\CompanyOwner;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceBatch;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
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

    public function refreshCurrencies($id, Request $request)
    {
        $company = Company::find($id);
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $xeroInterpreter->seedCurrencies($company);
        $xeroInterpreter->refreshAccounts($company);
        return response('success', 200);
    }

    public function store(Request $request)
    {
        $company = new Company;
        $company->name = $request->input('name');
        $company->cash_account_id = $request->input('cashAccountId');
        $company->setCompanyOwners($this->getCompanyOwners($request));
        $company->save();
        $company->companyOwners()->sync($company->getCompanyOwners());
        return $this->getResource($company);
    }

    private function getCompanyOwners(Request $request)
    {
        return array_map(function ($item) {
            if (isset($item['id']) || $item['id'] < 0) {
                $detail = new CompanyOwner();
            } else {
                $detail = CompanyOwner::find($item['id']);
            }
            $detail->name = $item['name'];
            $detail->account_id = $item['accountId'];
            return $detail;
        }, $request->input('companyOwners.data'));
    }

    public function update($id, Request $request)
    {
        $company = Company::find($id);
        $company->name = $request->input('name');
        $company->cash_account_id = $request->input('cashAccountId');
        $company->setCompanyOwners($this->getCompanyOwners($request));
        $company->save();
        $company->companyOwners()->sync($company->getCompanyOwners());
        return $this->getResource($company);
    }

    public function destroy($id)
    {
        $company = Company::find($id);
        if ($company->isXeroConnected()) {
            throw new GeneralApiException('Cannot Delete Company that has an active xero connection');
        }
        if ($company->hasUsers()) {
            throw new GeneralApiException('Cannot Delete Company that is connected to a user');
        }
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
                    'account_id' => $request->input("accountId")
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
        $company->xero_short_code = null;
        $company->save();
        $this->deleteCompanyData($company->id);
        return response('success', 200);
    }
    

    private function deleteCompanyData($companyId)
    {
        Invoice::where('company_id', $companyId)->get()->each(function ($invoice) {
            $invoice->fromXero = true;
            $invoice->delete();
        });

        InvoiceBatch::where('company_id', $companyId)->delete();

        Account::where('company_id', $companyId)->delete();

        Currency::where('company_id', $companyId)->delete();
    }

    public function updateBank($id, $bankId, Request $request)
    {
        $companyBank = \DB::transaction(function () use ($id, $bankId, $request) {
            $companyBank = CompanyBank::where('company_id', $id)->where('bank_id', $bankId)->first();
            $companyBank->account_number = $request->input('accountNumber');
            $companyBank->account_id = $request->input('accountId');
            $companyBank->save();
            return $companyBank;
        });

        return new CompanyBankResource($companyBank);
    }
}
