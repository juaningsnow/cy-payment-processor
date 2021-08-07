<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\Traits\DateParser;
use App\Http\Interpreters\XeroInterpreter;
use App\Http\Resources\CreditNoteResource;
use App\Http\Resources\CreditNoteResourceCollection;
use App\Models\Account;
use App\Models\Company;
use App\Models\CreditNote;
use App\Models\CreditNoteAllocation;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Utils\CompanyIndexFilter;
use BaseCode\Common\Controllers\ResourceApiController;
use Illuminate\Http\Request;

class CreditNoteApiController extends ResourceApiController
{
    use CompanyIndexFilter;
    use DateParser;
    
    const EXPORT_FILE_NAME = 'creditNotes.xlsx';

    protected $creditNote;

    public function __construct(CreditNote $creditNote)
    {
        parent::__construct($creditNote);
        $this->limit = 100;
    }

    public function getResource($item)
    {
        return new CreditNoteResource($item);
    }

    public function getResourceCollection($items)
    {
        return new CreditNoteResourceCollection($items);
    }

    public function syncXero($id)
    {
        $creditNote = CreditNote::find($id);
        $company = auth()->user()->getActiveCompany();
        $xero = resolve(XeroInterpreter::class);
        $xeroCreditNote = $xero->getCreditNote($creditNote->xero_credit_note_id, $company->xero_tenant_id);
        $this->updateCreditNote($xeroCreditNote);
        return response('success', 200);
    }

    public function update($id, Request $request)
    {
        $creditNote = CreditNote::find($id);
        $creditNote->triggerXero = true;
        if ($creditNote) {
            $creditNote->creditNoteAllocations()->sync($this->assembleAllocationsFromRequest($request));
        }
        $creditNote->save();
    }

    private function assembleAllocationsFromRequest(Request $request)
    {
        return array_map(function ($item) {
            if (isset($item['id']) || $item['id'] < 0) {
                $detail = new CreditNoteAllocation();
            } else {
                $detail = CreditNoteAllocation::find($item['id']);
            }
            $invoice = Invoice::find($item['invoiceId']);
            $amount = 0;
            if ($item['amount'] > 0) {
                $amount = $item['amount'];
            } else {
                if ($invoice->amount_due > 0) {
                    $amount = $invoice->amount_due;
                } else {
                    $amount = $invoice->total;
                }
            }
            $detail->invoice_id = $invoice->id;
            $detail->amount = $amount;
            return $detail;
        }, $request->input('creditNoteAllocations.data'));
    }

    public function refreshCreditNotes(Request $request)
    {
        $company = auth()->user()->getActiveCompany();
        $xero = resolve(XeroInterpreter::class);
        $creditNotes = $xero->getCreditNotes($company);
        $this->seedCreditNotes($creditNotes, $company);
        return response('success', 200);
    }

    private function seedCreditNotes(array $creditNotes, Company $company)
    {
        foreach ($creditNotes as $creditNote) {
            $count = CreditNote::where('xero_credit_note_id', $creditNote->CreditNoteID)->count();
            if ($count > 0) {
                $this->updateCreditNote($creditNote);
            } else {
                $this->createCreditNote($creditNote, $company);
            }
        }
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

    private function createCreditNote($xeroCreditNote, Company $company)
    {
        $supplier = Supplier::where('xero_contact_id', $xeroCreditNote->Contact->ContactID)->first();
        $currency = Currency::where('code', $xeroCreditNote->CurrencyCode)->where('company_id', $company->id)->first();
        if (!$supplier) {
            $supplier = $this->createSupplier($xeroCreditNote->Contact->ContactID, $company->xero_tenant_id);
        }
        $creditNote = new CreditNote();
        $creditNote->date = $xeroCreditNote->DateString;
        $creditNote->supplier_id = $supplier ? $supplier->id : null;
        $creditNote->currency_id = $currency ? $currency->id : null;
        $creditNote->company_id = $company->id;
        $creditNote->xero_credit_note_id = $xeroCreditNote->CreditNoteID;
        $creditNote->status = $xeroCreditNote->Status;
        $creditNote->total = $xeroCreditNote->Total;
        $creditNote->save();
        $creditNote->creditNoteAllocations()->sync($this->assembleAllocations($xeroCreditNote));
    }

    private function assembleAllocations($xeroCreditNote)
    {
        $allocations = [];
        if (property_exists($xeroCreditNote, "Allocations")) {
            foreach ($xeroCreditNote->Allocations as $allocation) {
                $invoice = Invoice::where('xero_invoice_id', $allocation->Invoice->InvoiceID)->first();
                $cyPayAllocation = new CreditNoteAllocation();
                $cyPayAllocation->invoice_id = $invoice ? $invoice->id : null;
                $cyPayAllocation->amount = $allocation->Amount;
                $allocations[] = $cyPayAllocation;
            }
        }
        return $allocations;
    }

    private function updateCreditNote($xeroCreditNote)
    {
        $creditNote = CreditNote::where('xero_credit_note_id', $xeroCreditNote->CreditNoteID)->first();
        if ($creditNote) {
            $supplier = Supplier::where('xero_contact_id', $xeroCreditNote->Contact->ContactID)->first();
            $currency = Currency::where('code', $xeroCreditNote->CurrencyCode)->where('company_id', $creditNote->company->id)->first();
            $creditNote->date = $xeroCreditNote->DateString;
            $creditNote->supplier_id = $supplier ? $supplier->id : null;
            $creditNote->currency_id = $currency ? $currency->id : null;
            $creditNote->company_id = $creditNote->company->id;
            $creditNote->xero_credit_note_id = $xeroCreditNote->CreditNoteID;
            $creditNote->status = $xeroCreditNote->Status;
            $creditNote->total = $xeroCreditNote->Total;
            $creditNote->save();
            $creditNote->creditNoteAllocations()->sync($this->assembleAllocations($xeroCreditNote));
        }
    }
}
