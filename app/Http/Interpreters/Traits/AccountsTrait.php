<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Account;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;

trait AccountsTrait
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

    public function seedAccounts()
    {
        $accounts = $this->getAccounts();
        foreach ($accounts as $account) {
            Account::create([
                'xero_account_id' => $account->AccountID,
                'name' => $account->Name,
                'code' => $account->Code,
            ]);
        }
    }
}
