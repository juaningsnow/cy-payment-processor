<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\Traits\DateParser;
use App\Http\Interpreters\XeroInterpreter;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceResourceCollection;
use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyOwner;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceBatch;
use App\Models\InvoiceCredit;
use App\Models\InvoicePayment;
use App\Models\InvoiceXeroAttachment;
use App\Models\Supplier;
use App\Utils\CompanyIndexFilter;
use BaseCode\Common\Controllers\ResourceApiController;
use BaseCode\Common\Exceptions\GeneralApiException;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InvoiceApiController extends ResourceApiController
{
    use CompanyIndexFilter;
    use DateParser;
    
    const EXPORT_FILE_NAME = 'invoices.xlsx';

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
        $this->limit = 100;
    }

    public function getResource($item)
    {
        return new InvoiceResource($item);
    }

    public function getResourceCollection($items)
    {
        return new InvoiceResourceCollection($items);
    }

    public function uploadAttachment($id, Request $request)
    {
        $invoice = Invoice::find($id);
        $xero = resolve(XeroInterpreter::class);
        $xero->uploadAttachment($invoice, $request);
        return response('success', 200);
    }

    public function storeMultipleInvoice(Request $request)
    {
        $invoices = $this->getInvoicesFromForm($request);
        foreach ($invoices as $invoice) {
            $invoice->save();
        };
        return $this->getResourceCollection(collect($invoices));
    }

    public function destroyMultiple(Request $request)
    {
        $invoices = array_map(function ($item) {
            $invoice = Invoice::find($item['id']);
            return $invoice;
        }, $request->input('selected'));
        foreach ($invoices as $invoice) {
            $invoice->delete();
        }
        return response('success', 200);
    }

    public function addAttachment($id, Request $request)
    {
        $invoice = Invoice::find($id);
        $invoice->addMediaFromRequest('file')->toMediaCollection();
        return $this->getResource($invoice);
    }

    public function removeAttachment($id)
    {
        $media = Media::find($id);
        $media->delete();
        return response('success', 200);
    }

    public function markInvoiceAsPaid(Request $request)
    {
        $invoice = Invoice::find($request->input('invoiceId'));
        $paidBy = $request->input('paidBy');
        $companyOwner = CompanyOwner::find($request->input('ownerId'));
        $invoice->setPaidBy($paidBy);
        if ($paidBy == 'Other') {
            $invoice->setCompanyOwner($companyOwner);
        }
        $invoice->triggerXero = true;
        $invoice->save();
        return $this->getResource($invoice);
    }

    public function markAsPaid(Request $request)
    {
        $invoices = $this->getInvoices($request);
        $paidBy = $request->input('paidBy');
        $companyOwner = CompanyOwner::find($request->input('ownerId'));
        foreach ($invoices as $invoice) {
            $invoice->setPaidBy($paidBy);
            if ($paidBy == 'Other') {
                $invoice->setCompanyOwner($companyOwner);
            }
            $invoice->triggerXero = true;
            $invoice->save();
        }
        return $this->getResourceCollection(collect($invoices));
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if($invoice->invoicePayments()->exists()){
            throw new GeneralApiException('This invoice has Payment Record.');
        }
        $invoice->triggerXero = true;
        $invoice->delete();
        return response('success', 200);
    }

    public function refreshAttachments($id)
    {
        $invoice = Invoice::find($id);
        $company = auth()->user()->getActiveCompany();
        $xero = resolve(XeroInterpreter::class);
        $xeroInvoice = $xero->getInvoice($invoice->xero_invoice_id, $company->xero_tenant_id);
        $invoice->save();
        $xero->syncAttachments($xeroInvoice);
        $xero->syncPayments($xeroInvoice);
        $xero->syncCredits($xeroInvoice);
        return response('success', 200);
    }

    public function refreshInvoices(Request $request)
    {
        $company = auth()->user()->getActiveCompany();
        $xero = resolve(XeroInterpreter::class);
        $this->seedXeroInvoices($xero->retrieveAuthorisedInvoices($company->xero_tenant_id), $company->xero_tenant_id);
        $this->updateInvoices($xero->retrievePaidInvoices($company->xero_tenant_id), $company);
        $this->deleteInvoices($xero->retrieveVoidedInvoices($company->xero_tenant_id));
        return response('success', 200);
    }

    private function deleteInvoices(array $invoices)
    {
        foreach ($invoices as $invoice) {
            $cyPayInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID)->first();
            if ($cyPayInvoice) {
                $cyPayInvoice->fromXero = true;
                $cyPayInvoice->delete();
            }
        }
    }

    private function updateInvoices(array $invoices, $company)
    {
        foreach ($invoices as $invoice) {
            $cyPayInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID)->first();
            if ($cyPayInvoice) {
                $this->updateInvoice($cyPayInvoice, $invoice, $company->xero_tenant_id);
            }
        }
    }

    private function seedXeroInvoices(array $invoices, $tenantId)
    {
        foreach ($invoices as $invoice) {
            $cyPayInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID)->first();
            if ($cyPayInvoice) {
                $this->updateInvoice($cyPayInvoice, $invoice, $tenantId);
            } else {
                $this->createInvoice($invoice, $tenantId);
            }
        }
    }

    private function updateInvoice(Invoice $invoice, $xeroInvoice, $tenantId)
    {
        $supplier = Supplier::where('xero_contact_Id', $xeroInvoice->Contact->ContactID)->first();
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        if ($company) {
            $currency = Currency::where('code', $xeroInvoice->CurrencyCode)->where('company_id', $company->getId())->first();
            if (!$supplier) {
                $supplier = $this->createSupplier($xeroInvoice->Contact->ContactID, $tenantId);
            }
            $processorInvoice = Invoice::find($invoice->id);
            $processorInvoice->supplier_id = $supplier->id;
            $processorInvoice->date = new Carbon($xeroInvoice->DateString);
            $processorInvoice->invoice_number = $xeroInvoice->InvoiceNumber;
            $processorInvoice->total = $xeroInvoice->Total;
            $processorInvoice->amount_due = $xeroInvoice->AmountDue;
            $processorInvoice->amount_paid = $xeroInvoice->AmountPaid;
            $processorInvoice->company_id = $company->getId();
            $processorInvoice->xero_invoice_id = $xeroInvoice->InvoiceID;
            $processorInvoice->currency_id = $currency ? $currency->id : null;
            $processorInvoice->fromXero = true;
            if ($processorInvoice->total == $processorInvoice->amount_paid) {
                $processorInvoice->paid = true;
            }
            $processorInvoice->status = $processorInvoice->computeStatus();
            $processorInvoice->save();
            if (property_exists($xeroInvoice, 'Payments')) {
                $processorInvoice->invoicePayments()->sync($this->assembleInvoicePayments($xeroInvoice));
            }
            if (property_exists($xeroInvoice, 'CreditNotes')) {
                $processorInvoice->invoiceCredits()->sync($this->assembleInvoiceCredits($xeroInvoice->CreditNotes));
            }
            if (property_exists($xeroInvoice, 'Attachments')) {
                $processorInvoice->invoiceXeroAttachments()->sync($this->assembleInvoiceAttachments($xeroInvoice));
            }
        }
    }

    private function createInvoice($invoice, $tenantId)
    {
        $supplier = Supplier::where('xero_contact_Id', $invoice->Contact->ContactID)->first();
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        $currency = Currency::where('code', $invoice->CurrencyCode)->where('company_id', $company->getId())->first();
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
        $processorInvoice->company_id = $company->getId();
        $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
        $processorInvoice->currency_id = $currency ? $currency->id : null;
        $processorInvoice->fromXero = true;
        if ($processorInvoice->total == $processorInvoice->amount_paid) {
            $processorInvoice->paid = true;
        }
        $processorInvoice->status = $processorInvoice->computeStatus();
        $processorInvoice->save();
        if (property_exists($invoice, 'Payments')) {
            $processorInvoice->invoicePayments()->sync($this->assembleInvoicePayments($invoice));
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

        $company = Company::where('xero_tenant_id', $tenantId)->first();
        $supplier = new Supplier();
        $supplier->fromXero = true;
        $supplier->name = $contact->Name;
        $supplier->payment_type = "FAST";
        $supplier->email = $email;
        $supplier->xero_contact_id = $contact->ContactID;
        $supplier->company_id = $company->getId();
        $supplier->account_id = $account ? $account->getId() : null;
        $supplier->fromXero = true;
        $supplier->save();
        return $supplier;
    }

    private function deleteCompanyData($companyId)
    {
        Invoice::where('company_id', $companyId)->where('amount_due', '>', 0)->where('paid', false)->get()->each(function ($invoice) {
            $invoice->fromXero = true;
            $invoice->delete();
        });
    }


    public function update($id, Request $request)
    {
        if ($this->checkIfExists($request->input('invoiceNumber'), $request->input('supplierId'), $id)) {
            throw new GeneralApiException("Invoice: {$request->input('invoiceNumber')} already exists!");
        }
        $invoice = Invoice::find($id);
        $supplier = Supplier::find($request->input('supplierId'));
        $invoice->setSupplier($supplier);
        $invoice->setDate(new DateTime($request->input('date')));
        $invoice->setInvoiceNumber($request->input('invoiceNumber'));
        $invoice->total = $request->input('total');
        $invoice->setDescription($request->input('description'));
        $invoice->currency_id = $request->input('currencyId');
        $invoice->triggerXero = true;
        $invoice->save();
        return $this->getResource($invoice);
    }

    private function getInvoices(Request $request)
    {
        return array_map(function ($item) {
            $detail = Invoice::find($item['id']);
            return $detail;
        }, $request->input('selected'));
    }

    private function getInvoicesFromForm(Request $request)
    {
        return array_map(function ($item) use ($request) {
            if (isset($item['id']) || $item['id'] < 0) {
                $detail = new Invoice();
                if ($this->checkIfExists($item['invoiceNumber'], $item['supplierId'])) {
                    throw new GeneralApiException("Invoice: {$item['invoiceNumber']} already exists!");
                }
            } else {
                $detail = Invoice::find($item['id']);
                if ($this->checkIfExists($item['invoiceNumber'], $item['supplierId'], $item['id'])) {
                    throw new GeneralApiException("Invoice: {$item['invoiceNumber']} already exis1ts!");
                }
            }
            $currency = Currency::find($item['currencyId']);
            if (!$currency || $currency->code !== 'SGD') {
                throw new GeneralApiException('Currency input is missing or not SGD!');
            }
            $supplier = Supplier::find($item['supplierId']);

            if (!$supplier->getAccount()) {
                throw new GeneralApiException('Update '.$supplier->name.' Xero account.');
            }
            $detail->setSupplier($supplier);
            $detail->setDate(new DateTime($item['date']));
            $detail->setInvoiceNumber($item['invoiceNumber']);
            $detail->setTotal($item['amount']);
            $detail->setDescription($item['description']);
            $detail->setCurrency($currency);
            $detail->setCompany($request->user()->getActiveCompany());
            return $detail;
        }, $request->input('invoices.data'));
    }

    private function checkIfExists($invoiceNumber, $supplierId, $invoiceId = null)
    {
        $count = Invoice::where('invoice_number', $invoiceNumber)->where('supplier_id', $supplierId)->count();
        if ($invoiceId) {
            return $count > 1 ? true : false;
        }
        return $count > 0 ? true : false;
    }
}
