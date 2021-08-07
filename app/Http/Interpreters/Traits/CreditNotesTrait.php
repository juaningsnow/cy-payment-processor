<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Company;
use App\Models\CreditNote;
use App\Models\CreditNoteAllocation;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Supplier;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;

trait CreditNotesTrait
{
    public function getCreditNote($creditNoteId, $tenantId)
    {
        try {
            $url = $this->baseUrl."/CreditNotes/{$creditNoteId}";
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))
                ->get($url);
            $data = json_decode($response->getBody()->getContents());
            return $data->CreditNotes[0];
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function getCreditNotes(Company $company)
    {
        try {
            $url = $this->baseUrl."/CreditNotes?where=".urlencode('Type="ACCPAYCREDIT"');
            $response = Http::withHeaders($this->getTenantDefaultHeaders($company->xero_tenant_id))
                ->get($url);
            $data = json_decode($response->getBody()->getContents());
            return $data->CreditNotes;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function creditNoteAllocation(CreditNote $creditNote)
    {
        $allocations = $this->assembleCreditNoteAllocations($creditNote);
        foreach ($allocations as $allocation) {
            $this->updateCreditNote($creditNote, $allocation);
        }
        // $this->resyncCreditNote($creditNote);
    }

    public function updateCreditNote(CreditNote $creditNote, $data)
    {
        // dd($this->baseUrl.'/CreditNotes/'.$creditNote->xero_credit_note_id);
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($creditNote->company->xero_tenant_id))->withBody(
                json_encode($data),
                'application/json'
            )->put($this->baseUrl.'/CreditNotes/'.$creditNote->xero_credit_note_id);
            $data = json_decode($response->getBody()->getContents());
            return $data;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    private function assembleCreditNoteAllocations(CreditNote $creditNote)
    {
        return $creditNote->creditNoteAllocations->map(function ($allocation) {
            return [
                    'Amount' => $allocation->amount,
                    'Invoice' => [
                        'InvoiceID' => $allocation->invoice->xero_invoice_id,
                    ],
                ];
        })->all();
    }

    private function resyncCreditNote(CreditNote $creditNote)
    {
        $xeroCreditNote = $this->getCreditNote($creditNote->xero_credit_note_id, $creditNote->company->xero_tenant_id);
        $this->updateCyPayCreditNote($xeroCreditNote);
        return response('success', 200);
    }

    private function updateCyPayCreditNote($xeroCreditNote)
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
}
