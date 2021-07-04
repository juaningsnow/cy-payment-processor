<?php

namespace App\Listeners;

use App\Http\Interpreters\XeroInterpreter;
use App\Models\Account;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InvoiceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $invoice = $xeroInterpreter->getInvoice($event->invoiceId);
        if ($invoice->Type == 'ACCPAY') {
            if ($invoice->Status == "VOIDED") {
                $this->deleteInvoice($invoice->InvoiceID);
            }
            if ($invoice->Status == "AUTHORISED") {
                if ($event->isCreate) {
                    $this->createInvoice($invoice);
                } else {
                    $this->updateInvoice($invoice);
                }
            }
            if ($invoice->Status == "PAID") {
                $this->updateInvoicePayment($invoice);
            }
        }
    }

    private function updateInvoicePayment($invoice)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::first();
        if (!$supplier) {
            $supplier = $this->createSupplier($invoice->Contact);
        }
        $processorInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID);
        if (!$processorInvoice) {
            $this->createInvoice($invoice);
        } else {
            $processorInvoice->supplier_id = $supplier->id;
            $processorInvoice->date = new Carbon($invoice->DateString);
            $processorInvoice->invoice_number = $invoice->InvoiceNumber;
            $processorInvoice->amount = $invoice->Total;
            $processorInvoice->company_id = $company->id;
            $processorInvoice->status = $processorInvoice->computeStatus();
            $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
            $processorInvoice->xero_payment_id = $invoice->Payments[0]->PaymentID;
            $processorInvoice->paid = true;
            $processorInvoice->paid_by = "Paid on Xero";
            $processorInvoice->fromXero = true;
            $processorInvoice->save();
        }
    }

    private function createSupplier($contact)
    {
        $account = Account::where('code', $contact->PurchasesDefaultAccountCode)->first();
        $company = Company::first();
        $supplier = new Supplier();
        $supplier->fromXero = true;
        $supplier->name = $contact->Name;
        $supplier->payment_type = "FAST";
        $supplier->account_number = $contact->BankAccountDetails;
        $supplier->email = $contact->EmailAddress;
        $supplier->xero_contact_id = $contact->ContactID;
        $supplier->company_id = $company->id;
        $supplier->account_id = $account->id;
        $supplier->fromXero = true;
        $supplier->save();
        return $supplier;
    }

    private function updateInvoice($invoice)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::first();
        if (!$supplier) {
            $supplier = $this->createSupplier($invoice->Contact);
        }
        $processorInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID);
        if (!$processorInvoice) {
            $this->createInvoice($invoice);
        } else {
            $processorInvoice->supplier_id = $supplier->id;
            $processorInvoice->date = new Carbon($invoice->DateString);
            $processorInvoice->invoice_number = $invoice->InvoiceNumber;
            $processorInvoice->amount = $invoice->Total;
            $processorInvoice->company_id = $company->id;
            $processorInvoice->status = $processorInvoice->computeStatus();
            $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
            $processorInvoice->paid = false;
            $processorInvoice->paid_by = null;
            $processorInvoice->fromXero = true;
            $processorInvoice->save();
        }
    }

    private function createInvoice($invoice)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::first();
        if (!$supplier) {
            $supplier = $this->createSupplier($invoice->Contact);
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

    private function deleteInvoice($xeroInvoiceId)
    {
        $invoice = Invoice::where('xero_invoice_id', $xeroInvoiceId)->first();
        if ($invoice) {
            $invoice->delete();
        }
    }
}
