<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Supplier;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait ContactsTrait
{
    public function getContact($contactId, $tenantId)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))->get($this->baseUrl.'/Contacts/'.$contactId);
            $data = json_decode($response->getBody()->getContents());
            if (is_object($data)) {
                if (property_exists($data, 'Contacts')) {
                    return $data->Contacts[0];
                } else {
                    Log::info($data);
                    return null;
                }
            } else {
                Log::info($data);
                return null;
            }
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function createContact(Supplier $supplier)
    {
        $body = [
            'Name' => $supplier->name,
            'EmailAddress' => $supplier->email,
            'BankAccountDetails' => $supplier->account_number."_".$supplier->bank->swift,
            'PurchasesDefaultAccountCode' => $supplier->account->code
        ];

        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($supplier->company->xero_tenant_id))->withBody(
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
            'PurchasesDefaultAccountCode' => $supplier->account->code
        ];

        try {
            Http::withHeaders($this->getTenantDefaultHeaders($supplier->company->xero_tenant_id))->withBody(
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
            Http::withHeaders($this->getTenantDefaultHeaders($supplier->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Contacts');
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function retrieveContactId($email, $tenantId)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))
                ->get($this->baseUrl.'/Contacts?where=EmailAddress="'.$email.'"');
            $data = json_decode($response->getBody()->getContents());
            if (count($data->Contacts) > 0) {
                return $data->Contacts[0]->ContactID;
            }
            return null;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }
}
