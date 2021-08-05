<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Company;
use App\Models\Currency;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;

trait CurrencyTrait
{
    public function getCurrencies(Company $company)
    {
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($company->xero_tenant_id))
                ->get($this->baseUrl.'/Currencies');
            $data = json_decode($response->getBody()->getContents());
            return $data->Currencies;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function seedCurrencies(Company $company)
    {
        Currency::where('company_id', $company->getId())->delete();
        $currencies = $this->getCurrencies($company);
        $seeds = [];
        foreach ($currencies as $currency) {
            if (property_exists($currency, 'Code')) {
                $seeded = Currency::create([
                    'code' => $currency->Code,
                    'description' => $currency->Description,
                    'company_id' => $company->getId()
                ]);
                $seeds[] = $seeded;
            }
        }
        return $seeds;
    }
}
