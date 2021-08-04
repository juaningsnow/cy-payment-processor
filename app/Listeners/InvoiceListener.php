<?php

namespace App\Listeners;

use App\Http\Interpreters\XeroInterpreter;
use App\Models\Account;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceCredit;
use App\Models\InvoicePayment;
use App\Models\InvoiceXeroAttachment;
use App\Models\Supplier;
use Carbon\Carbon;

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
        $invoice = $xeroInterpreter->getInvoice($event->invoiceId, $event->tenantId);
        if ($invoice) {
            if ((bool)$invoice->HasAttachments) {
                $xeroInterpreter->syncAttachments($invoice);
                $xeroInterpreter->syncPayments($invoice);
                $xeroInterpreter->syncCredits($invoice);
            }
            if ($invoice->Type == 'ACCPAY') {
                if ($invoice->Status == "VOIDED") {
                    $this->deleteInvoice($invoice->InvoiceID);
                }
                if ($invoice->Status == "AUTHORISED") {
                    if ($event->isCreate) {
                        $this->createInvoice($invoice, $event->tenantId);
                    } else {
                        $this->updateInvoice($invoice, $event->tenantId);
                    }
                }
                if ($invoice->Status == "PAID") {
                    $this->updateInvoicePayment($invoice, $event->tenantId);
                }
            }
        }
    }

    private function updateInvoicePayment($invoice, $tenantId)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        if (!$supplier) {
            $supplier = $this->createSupplier($invoice->Contact, $tenantId);
        }
        $processorInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID)->first();
        if (!$processorInvoice) {
            $this->createInvoice($invoice, $tenantId);
        } else {
            if ($supplier) {
                $processorInvoice->supplier_id = $supplier->id;
                $processorInvoice->date = new Carbon($invoice->DateString);
                $processorInvoice->invoice_number = $invoice->InvoiceNumber;
                $processorInvoice->total = $invoice->Total;
                $processorInvoice->amount_due = $invoice->AmountDue;
                $processorInvoice->amount_paid = $invoice->AmountPaid;
                $processorInvoice->company_id = $company->id;
                $processorInvoice->status = $processorInvoice->computeStatus();
                $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
                $processorInvoice->paid = true;
                $processorInvoice->paid_by = "Paid on Xero";
                $processorInvoice->fromXero = true;
                $processorInvoice->save();
            }
        }
    }

    private function createSupplier($contact, $tenantId)
    {
        $account = null;
        if (property_exists($contact, 'PurchasesDefaultAccountCode')) {
            $account = Account::where('code', $contact->PurchasesDefaultAccountCode)->first();
        }
        if (property_exists($contact, 'Name')) {
            $company = Company::where('xero_tenant_id', $tenantId)->first();
            $supplier = new Supplier();
            $supplier->fromXero = true;
            $supplier->name = $contact->Name;
            $supplier->payment_type = "FAST";
            $supplier->email = $contact->EmailAddress;
            $supplier->xero_contact_id = $contact->ContactID;
            $supplier->company_id = $company->id;
            $supplier->account_id = $account ? $account->id : null;
            $supplier->fromXero = true;
            $supplier->save();
            return $supplier;
        }
    }

    private function updateInvoice($invoice, $tenantId)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        if (!$supplier) {
            $supplier = $this->createSupplier($invoice->Contact, $tenantId);
        }
        $processorInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID)->first();
        if (!$processorInvoice) {
            $this->createInvoice($invoice, $tenantId);
        } else {
            if ($supplier) {
                $processorInvoice->supplier_id = $supplier->id;
                $processorInvoice->date = new Carbon($invoice->DateString);
                $processorInvoice->invoice_number = $invoice->InvoiceNumber;
                $processorInvoice->total = $invoice->Total;
                $processorInvoice->amount_due = $invoice->AmountDue;
                $processorInvoice->amount_paid = $invoice->AmountPaid;
                $processorInvoice->company_id = $company->id;
                $processorInvoice->status = $processorInvoice->computeStatus();
                $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
                $processorInvoice->paid = false;
                $processorInvoice->paid_by = null;
                $processorInvoice->fromXero = true;
                $processorInvoice->save();
            }
        }
    }

    private function createInvoice($invoice, $tenantId)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        $currency = Currency::where('code', $invoice->CurrencyCode)->where('company_id', $company->id)->first();
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
        $processorInvoice->currency_id = $currency ? $currency->id : null;
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
        if ($invoice->HasAttachments) {
            $processorInvoice->invoiceXeroAttachments()->sync($this->assembleInvoiceAttachments($invoice));
        }
    }

    private function deleteInvoice($xeroInvoiceId)
    {
        $invoice = Invoice::where('xero_invoice_id', $xeroInvoiceId)->first();
        if ($invoice) {
            $invoice->delete();
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

    private function assembleInvoicePayments($payments)
    {
        return collect(array_map(function ($item) {
            $payment = new InvoicePayment();
            $payment->date =  $this->parseDate($item->Date);
            $payment->xero_payment_id = $item->PaymentID;
            $payment->amount = $item->Amount;
            return $payment;
        }, $payments));
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
}
