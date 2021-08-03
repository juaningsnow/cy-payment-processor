<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Account;
use App\Models\Company;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;

trait AccountsTrait
{
    public function getAccounts(Company $company)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($company->xero_tenant_id))
                ->get($this->baseUrl.'/Accounts');
            $data = json_decode($response->getBody()->getContents());
            return $data->Accounts;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function seedAccounts(Company $company)
    {
        $accounts = $this->getAccounts($company);
        $seeds = [];
        foreach ($accounts as $account) {
            if (property_exists($account, 'Code')) {
                $seeded = Account::create([
                    'xero_account_id' => $account->AccountID,
                    'name' => $account->Name,
                    'code' => $account->Code,
                    'company_id' => $company->id
                ]);
                $seeds[] = $seeded;
            }
        }
        return $seeds;
    }

    public function refreshAccounts(Company $company)
    {
        Account::where('company_id', $company->id)->delete();
        $accounts = $this->getAccounts($company);
        $seeds = [];
        foreach ($accounts as $account) {
            if (property_exists($account, 'Code')) {
                $seeded = Account::create([
                    'xero_account_id' => $account->AccountID,
                    'name' => $account->Name,
                    'code' => $account->Code,
                    'company_id' => $company->id
                ]);
                $seeds[] = $seeded;
            }
        }
        return $seeds;
    }
}
