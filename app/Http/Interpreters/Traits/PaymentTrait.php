<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Invoice;
use BaseCode\Common\Exceptions\GeneralApiException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

trait PaymentTrait
{
    public function getAccounts()
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders())
                ->get($this->baseUrl.'/Accounts');
            $data = json_decode($response->getBody()->getContents());
            return $data->Accounts;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function makePayment(Invoice $invoice)
    {
        try {
            $body = [
                'Invoice' => [
                    'InvoiceId' => $invoice->xero_invoice_id
                ],
                "Account" => [
                    "Code" => $invoice->supplier->account->code,
                ],
                "Date" => Carbon::now()->toDateTimeLocalString()
            ];
            // Http::withHeaders($this->getTenantDefaultHeaders())->withBody(
            //     json_encode($body), 'application/json'
            // )->
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function archiveContact(Supplier $supplier)
    {
        $body = [
            'ContactID' => $supplier->xero_contact_id,
            'ContactStatus' => "ARCHIVED",
        ];

        try {
            Http::withHeaders($this->getTenantDefaultHeaders())->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Contacts');
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }
}
