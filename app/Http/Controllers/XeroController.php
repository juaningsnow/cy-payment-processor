<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
use App\Models\Account;
use App\Models\Company;
use App\Models\Config;
use App\Models\Invoice;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class XeroController extends Controller
{
    public function status()
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $tokenIsValid = $xeroInterpreter->checkIfTokenIsValid();
        $companyIsConnected = auth()->user()->getActiveCompany()->isXeroConnected();
        $connected = $tokenIsValid && $companyIsConnected ? true : false;
        $authUrl = $xeroInterpreter->getAuthorizationUrl();
        return view('xero', [
           'authUrl' => $authUrl, 'connected' => $connected, 'title' => 'Xero API Connection Status'
        ]);
    }

    public function callback(Request $request)
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $code = $request->get('code');
        if ($code) {
            $response = $xeroInterpreter->exchangeToken($code);
            $config = Config::first();
            $config->access_token = $response->access_token;
            $config->refresh_token = $response->refresh_token;
            $config->save();
            $tokenParts = explode(".", $response->access_token);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);
            $company = auth()->user()->getActiveCompany();
            $company->auth_event_id = $jwtPayload->authentication_event_id;
            $tenantDetails = $xeroInterpreter->getTenantConnection($jwtPayload->authentication_event_id);
            $company->xero_connection_id = $tenantDetails->id;
            $company->xero_tenant_id = $tenantDetails->tenantId;
            $company->save();
            if (Account::where('company_id', $company->id)->count() < 1) {
                $xeroInterpreter->seedAccounts($company);
            }
            $this->seedXeroInvoices($xeroInterpreter->retrieveAuthorisedInvoices($tenantDetails->tenantId), $tenantDetails->tenantId);
            return redirect()->route('xero_status');
        }
    }

    private function seedXeroInvoices(array $invoices, $tenantId)
    {
        foreach ($invoices as $invoice) {
            $this->createInvoice($invoice, $tenantId);
        }
    }

    private function createInvoice($invoice, $tenantId)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        if (!$supplier) {
            $supplier = $this->createSupplier($invoice->Contact->ContactID, $tenantId);
        }
        $processorInvoice = new Invoice();
        $processorInvoice->supplier_id = $supplier->id;
        $processorInvoice->date = new Carbon($invoice->DateString);
        $processorInvoice->invoice_number = $invoice->InvoiceNumber;
        $processorInvoice->amount = $invoice->Total;
        $processorInvoice->company_id = $company->id;
        $processorInvoice->status = $processorInvoice->computeStatus();
        $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
        $processorInvoice->fromXero = true;
        $processorInvoice->save();
    }

    private function createSupplier($contactId, $tenantId)
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $contact = $xeroInterpreter->getContact($contactId, $tenantId);
        $account = null;
        if (property_exists($contact, 'PurchasesDefaultAccountCode')) {
            $account = Account::where('code', $contact->PurchasesDefaultAccountCode)->first();
        }
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        $supplier = new Supplier();
        $supplier->fromXero = true;
        $supplier->name = $contact->Name;
        $supplier->payment_type = "FAST";
        $supplier->email = $contact->EmailAddress ? $contact->EmailAddress : null;
        $supplier->xero_contact_id = $contact->ContactID;
        $supplier->company_id = $company->id;
        $supplier->account_id = $account ? $account->id : null;
        $supplier->fromXero = true;
        $supplier->save();
        return $supplier;
    }
}
