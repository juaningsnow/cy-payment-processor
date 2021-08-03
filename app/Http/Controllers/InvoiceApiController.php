<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceResourceCollection;
use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyOwner;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceBatch;
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

    public function markAsPaid(Request $request)
    {
        $invoices = $this->getInvoices($request);
        $paidBy = $request->input('paidBy');
        $companyOwner = CompanyOwner::find($request->input('ownerId'));
        foreach ($invoices as $invoice) {
            $invoice->setPaid(true);
            $invoice->setPaidBy($paidBy);
            if ($paidBy == 'Owner') {
                $invoice->setCompanyOwner($companyOwner);
            }
            $invoice->save();
        }
        return $this->getResourceCollection(collect($invoices));
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        $invoice->delete();
        return response('success', 200);
    }

    public function refreshAttachments($id)
    {
        $invoice = Invoice::find($id);
        $company = auth()->user()->getActiveCompany();
        $xero = resolve(XeroInterpreter::class);
        $xeroInvoice = $xero->getInvoice($invoice->xero_invoice_id, $company->xero_tenant_id);
        $xero->syncAttachments($xeroInvoice);
        $xero->syncPayments($xeroInvoice);
        return response('success', 200);
    }

    public function refreshInvoices(Request $request)
    {
        $company = auth()->user()->getActiveCompany();
        $xero = resolve(XeroInterpreter::class);
        $this->deleteCompanyData($company->id);
        $this->seedXeroInvoices($xero->retrieveAuthorisedInvoices($company->xero_tenant_id), $company->xero_tenant_id);
        return response('success', 200);
    }

    private function seedXeroInvoices(array $invoices, $tenantId)
    {
        foreach ($invoices as $invoice) {
            $this->createInvoice($invoice, $tenantId);
        }
    }

    private function createInvoice($invoice, $tenantId)
    {
        $supplier = Supplier::where('xero_contact_id', $invoice->Contact->ContactID)->first();
        $company = Company::where('xero_tenant_id', $tenantId)->first();
        $sgd = Currency::where('code', 'SGD')->first();
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
        $processorInvoice->status = $processorInvoice->computeStatus();
        $processorInvoice->xero_invoice_id = $invoice->InvoiceID;
        $processorInvoice->currency_id = $sgd->id;
        $processorInvoice->fromXero = true;
        $processorInvoice->save();
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
        $supplier->company_id = $company->id;
        $supplier->account_id = $account ? $account->id : null;
        $supplier->fromXero = true;
        $supplier->save();
        return $supplier;
    }

    private function deleteCompanyData($companyId)
    {
        InvoiceBatch::where('company_id', $companyId)->get()->each(function ($batch) {
            $batch->delete();
        });

        Invoice::where('company_id', $companyId)->get()->each(function ($invoice) {
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
        $invoice->setAmount($request->input('total'));
        $invoice->setDescription($request->input('description'));
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
