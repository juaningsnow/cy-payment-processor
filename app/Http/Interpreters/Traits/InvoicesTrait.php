<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Invoice;
use BaseCode\Common\Exceptions\GeneralApiException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

trait InvoicesTrait
{
    public function getInvoice($invoiceId)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders())->get($this->baseUrl.'/Invoices/'.$invoiceId);
            $data = json_decode($response->getBody()->getContents());
            // dd($data);
            return $data->Invoices[0];
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function createInvoice(Invoice $invoice)
    {
        $contactId = $invoice->supplier->xero_contact_id ? $invoice->supplier->xero_contact_id : $this->retrieveContactId($invoice->supplier->email);
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
            $response = Http::withHeaders($this->getTenantDefaultHeaders())->withBody(
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

        $contactId = $invoice->supplier->xero_contact_id ? $invoice->supplier->xero_contact_id : $this->retrieveContactId($invoice->supplier->email);
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
            $response = Http::withHeaders($this->getTenantDefaultHeaders())->withBody(
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
            $response = Http::withHeaders($this->getTenantDefaultHeaders())->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Invoices');
            return true;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
            return false;
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
                $response = Http::withHeaders($this->getTenantDefaultHeaders())->withBody(
                    json_encode($body),
                    'application/json'
                )->post($this->baseUrl.'/Invoices/'.$invoice->xero_invoice_id);
                return $response;
            } catch (Exception $e) {
                throw new GeneralApiException($e);
            }
        }
    }
}
