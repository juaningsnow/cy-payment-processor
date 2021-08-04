<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\Traits\DateParser;
use App\Http\Interpreters\XeroInterpreter;
use App\Models\Account;
use App\Models\Company;
use App\Models\Config;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceCredit;
use App\Models\InvoicePayment;
use App\Models\InvoiceXeroAttachment;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class XeroController extends Controller
{
    use DateParser;
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
            $organisation = $xeroInterpreter->getOrganization($tenantDetails->tenantId);
            $company->xero_short_code = $organisation->ShortCode;
            $company->save();
            if (Account::where('company_id', $company->id)->count() < 1) {
                $xeroInterpreter->seedAccounts($company);
            }
            $xeroInterpreter->seedCurrencies($company);
            $this->seedXeroInvoices($xeroInterpreter->retrieveAuthorisedInvoices($tenantDetails->tenantId), $tenantDetails->tenantId);
            $this->seedXeroInvoices($xeroInterpreter->retrievePaidInvoices($tenantDetails->tenantId), $tenantDetails->tenantId);
            
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
        $currency = Currency::where('code', $invoice->CurrencyCode)->first();
        if (!$supplier) {
            $supplier = $this->createSupplier($invoice->Contact->ContactID, $tenantId);
        }
        $processorInvoice = new Invoice();
        $processorInvoice->supplier_id = $supplier->id;
        $processorInvoice->date = new Carbon($invoice->DateString);
        $processorInvoice->invoice_number = $invoice->InvoiceNumber;
        $processorInvoice->total = $invoice->Total;
        $processorInvoice->amount_due = $invoice->AmountDue;
        $processorInvoice->amount_paid = $invoice->AmountPaid;
        $processorInvoice->company_id = $company->id;
        $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
        $processorInvoice->currency_id = $currency->id;
        $processorInvoice->fromXero = true;
        if ($processorInvoice->total == $processorInvoice->amount_paid) {
            $processorInvoice->paid = true;
        }
        $processorInvoice->status = $processorInvoice->computeStatus();
        $processorInvoice->save();
        if (property_exists($invoice, 'Payments')) {
            $processorInvoice->invoicePayments()->sync($this->assembleInvoicePayments($invoice->Payments));
        }
        if (property_exists($invoice, 'CreditNotes')) {
            $processorInvoice->invoiceCredits()->sync($this->assembleInvoiceCredits($invoice->CreditNotes));
        }
        if (property_exists($invoice, 'Attachments')) {
            $processorInvoice->invoiceXeroAttachments()->sync($this->assembleInvoiceAttachments($invoice));
        }
    }

    private function assembleInvoiceCredits($creditNotes)
    {
        return collect(array_map(function ($item) {
            $credit = new InvoiceCredit();
            $credit->date =  $this->parseDate($item->Date);
            $credit->xero_credit_id = $item->CreditNoteID;
            $credit->amount = $item->AppliedAmount;
            return $credit;
        }, $creditNotes));
    }

    private function assembleInvoiceAttachments($invoice)
    {
        return array_map(function ($attachment) {
            return new InvoiceXeroAttachment([
                'name' => $attachment->FileName,
                'url' => $attachment->Url
            ]);
        }, $invoice->Attachments);
    }

    public function assembleInvoicePayments($invoice)
    {
        $allPayments = collect([]);
        if (property_exists($invoice, 'Payments')) {
            $payments = collect(array_map(function ($item) {
                $payment = new InvoicePayment();
                $payment->date =  $this->parseDate($item->Date);
                $payment->xero_payment_id = $item->PaymentID;
                $payment->amount = $item->Amount;
                return $payment;
            }, $invoice->Payments));
        }
        if (property_exists($invoice, 'Overpayments')) {
            $overPayments = collect(array_map(function ($item) {
                $payment = new InvoicePayment();
                $payment->date =  $this->parseDate($item->Date);
                $payment->xero_payment_id = $item->OverpaymentID;
                $payment->amount = $item->AppliedAmount;
                return $payment;
            }, $invoice->Overpayments));
        }
        $allPayments = $payments->merge($overPayments);

        return $allPayments;
    }

    private function createSupplier($contactId, $tenantId)
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $contact = $xeroInterpreter->getContact($contactId, $tenantId);
        $account = null;
        $email = null;
        if (property_exists($contact, 'PurchasesDefaultAccountCode')) {
            $account = Account::where('code', $contact->PurchasesDefaultAccountCode)->first();
        }

        if (property_exists($contact, 'EmailAddress')) {
            $email = $contact->EmailAddress;
        }
        if ($contact) {
            $company = Company::where('xero_tenant_id', $tenantId)->first();
            $supplier = new Supplier();
            $supplier->fromXero = true;
            $supplier->name = $contact->Name;
            $supplier->payment_type = "FAST";
            $supplier->email = $email;
            $supplier->xero_contact_id = $contact->ContactID;
            $supplier->company_id = $company->id;
            $supplier->account_id = $account ? $account->id : null;
            $supplier->fromXero = true;
            $supplier->save();
            return $supplier;
        }
    }
}
