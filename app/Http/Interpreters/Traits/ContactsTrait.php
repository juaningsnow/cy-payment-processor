<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Supplier;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;

trait ContactsTrait
{
    public function createContact(Supplier $supplier)
    {
        $body = [
            'Name' => $supplier->name,
            'EmailAddress' => $supplier->email,
            'BankAccountDetails' => $supplier->account_number."_".$supplier->bank->swift,
        ];

        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders())->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Contacts');
            $data = json_decode($response->getBody()->getContents());

            $supplier->xero_contact_id = $data->Contacts[0]->ContactID;
            $supplier->save();
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function updateContact(Supplier $supplier)
    {
        $body = [
            'ContactID' => $supplier->xero_contact_id,
            'Name' => $supplier->name,
            'EmailAddress' => $supplier->email,
            'BankAccountDetails' => $supplier->account_number."_".$supplier->bank->swift,
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
