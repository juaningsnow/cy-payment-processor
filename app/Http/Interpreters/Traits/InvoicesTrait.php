<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Invoice;
use App\Models\InvoiceXeroAttachment;
use BaseCode\Common\Exceptions\GeneralApiException;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait InvoicesTrait
{
    public function getInvoice($invoiceId, $tenantId)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))->get($this->baseUrl.'/Invoices/'.$invoiceId);
            $data = json_decode($response->getBody()->getContents());
            return $data->Invoices[0];
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function getInvoiceAttachments($invoiceId, $tenantId)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))->get($this->baseUrl.'/Invoices/'.$invoiceId.'/Attachments');
            $data = json_decode($response->getBody()->getContents());
            return $data->Attachments;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function createInvoice(Invoice $invoice)
    {
        $contactId = $invoice->supplier->xero_contact_id ? $invoice->supplier->xero_contact_id : $this->retrieveContactId($invoice->supplier->email, $invoice->company->xero_tenant_id);
        if (!$contactId) {
            throw new GeneralApiException("Supplier does not exists in xero!");
        }
        $date = new Carbon($invoice->date);
        $body = [
            'Type' => 'ACCPAY',
            'Contact' => [
                'ContactId' => $contactId,
            ],
            "DateString" => $date->toDateTimeLocalString(),
            "DueDateString" => $date->toDateTimeLocalString(),
            "InvoiceNumber" => $invoice->invoice_number.'-'.$invoice->supplier->id,
            "LineItems" => [[
                "Description" => $invoice->description ? $invoice->description : '.',
                "Quantity" => '1',
                "TaxType" => "NONE",
                "UnitAmount" => $invoice->amount,
                "LineAmount" => $invoice->amount,
                "AccountCode" => $invoice->supplier->account->code,
            ]],
            "Status" => "AUTHORISED",
        ];
        
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoice->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Invoices');
            $data = json_decode($response->getBody()->getContents());
            $invoice->xero_invoice_id = $data->Invoices[0]->InvoiceID;
            $invoice->save();
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function updateInvoice(Invoice $invoice)
    {
        if (!$invoice->xero_invoice_id) {
            throw new GeneralApiException("Does not exists in xero!");
        }

        $contactId = $invoice->supplier->xero_contact_id ? $invoice->supplier->xero_contact_id : $this->retrieveContactId($invoice->supplier->email, $invoice->company->xero_tenant_id);
        if (!$contactId) {
            throw new GeneralApiException("Supplier does not exists in xero!");
        }

        $date = new Carbon($invoice->date);
        $body = [
            'InvoiceID' => $invoice->xero_invoice_id,
            'Type' => 'ACCPAY',
            'Contact' => [
                'ContactId' => $contactId,
            ],
            "DateString" => $date->toDateTimeLocalString(),
            "DueDateString" => $date->toDateTimeLocalString(),
            "InvoiceNumber" => $invoice->invoice_number.'-'.$invoice->supplier->id,
            "LineItems" => [[
                "Description" => $invoice->description ? $invoice->description : '.',
                "Quantity" => '1',
                "TaxType" => "NONE",
                "UnitAmount" => $invoice->amount,
                "LineAmount" => $invoice->amount,
                "AccountCode" => $invoice->supplier->account->code,
            ]],
            "Status" => "AUTHORISED",
        ];
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoice->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Invoices');
            $data = json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    private function updateInvoiceNumber(Invoice $invoice)
    {
        $body = [
            'InvoiceID' => $invoice->xero_invoice_id,
            "InvoiceNumber" => $invoice->invoice_number.'-'.$invoice->supplier->id.today()->format('mdyhis'),
            "LineItems" => [[
                "Description" => $invoice->description ? $invoice->description : '.',
                "Quantity" => '1',
                "TaxType" => "NONE",
                "UnitAmount" => $invoice->amount,
                "LineAmount" => $invoice->amount,
                "AccountCode" => $invoice->supplier->account->code,
            ]],
            "Status" => "AUTHORISED",
        ];
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoice->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Invoices');
            return true;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
            return false;
        }
    }

    public function downloadAttachment(InvoiceXeroAttachment $invoiceXeroAttachment)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoiceXeroAttachment->invoice->company->xero_tenant_id))->get($invoiceXeroAttachment->url);
            return $response;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function uploadAttachment(Invoice $invoice, Request $request)
    {
        try {
            $header = [
                'Authorization' => "Bearer {$this->config->access_token}",
                'Content-Type' => $request->file->getMimeType(),
                'Content-Length' => 10496,
                'Accept' => 'text/xml',
                'Xero-tenant-id' => $this->config->xero_tenant_id
            ];
            Http::withHeaders($header)->attach(
                'attachment',
                file_get_contents($request->file->getRealPath()),
                $request->file->getClientOriginalName()
            )->post($this->baseUrl.'/Invoices/'.$invoice->xero_invoice_id.'/Attachments/'. $request->file->getClientOriginalName());
            $xeroInvoice = $this->getInvoice($invoice->xero_invoice_id, $invoice->company->xero_tenant_id);
            $this->syncAttachments($xeroInvoice);
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function voidInvoice(Invoice $invoice)
    {
        $body = [
            "InvoiceID" => $invoice->xero_invoice_id,
            "Status" => "VOIDED",
        ];
        if ($this->updateInvoiceNumber($invoice)) {
            try {
                $response = Http::withHeaders($this->getTenantDefaultHeaders($invoice->company->xero_tenant_id))->withBody(
                    json_encode($body),
                    'application/json'
                )->post($this->baseUrl.'/Invoices/'.$invoice->xero_invoice_id);
                return $response;
            } catch (Exception $e) {
                throw new GeneralApiException($e);
            }
        }
    }

    public function syncAttachments($invoice)
    {
        $processorInvoice = Invoice::where('xero_invoice_id', $invoice->InvoiceID)->first();
        $attachments = $this->assembleInvoiceAttachments($invoice);
        $processorInvoice->invoiceXeroAttachments()->sync($attachments);
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
