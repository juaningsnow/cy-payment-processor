<?php

namespace App\Http\Interpreters;

use App\Http\Interpreters\Traits\AccountsTrait;
use App\Http\Interpreters\Traits\ContactsTrait;
use App\Http\Interpreters\Traits\CurrencyTrait;
use App\Http\Interpreters\Traits\DateParser;
use App\Http\Interpreters\Traits\InvoicesTrait;
use App\Http\Interpreters\Traits\PaymentTrait;
use App\Models\Account;
use App\Models\Config;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;

class XeroInterpreter
{
    use ContactsTrait;
    use InvoicesTrait;
    use AccountsTrait;
    use PaymentTrait;
    use CurrencyTrait;
    use DateParser;

    protected $tokenUrl = 'https://identity.xero.com/connect/token';
    protected $authorizationUrl = 'https://login.xero.com/identity/connect/authorize';
    protected $connectionCheckUrl = 'https://api.xero.com/connections';
    protected $baseUrl = 'https://api.xero.com/api.xro/2.0';
    protected $config;

    public function __construct()
    {
        $this->config = Config::first();
    }

    public function refreshToken()
    {
        $headers = [
            'Authorization' => "Basic ".base64_encode($this->config->client_id.':'.$this->config->client_secret)
        ];
        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->config->refresh_token
        ];

        try {
            $response = Http::withHeaders($headers)->asForm()->post($this->tokenUrl, $body);
            $data = json_decode($response->getBody()->getContents());
            $config = Config::first();
            $config->access_token = $data->access_token;
            $config->refresh_token = $data->refresh_token;
            $config->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public function getTenantConnection($authEventId)
    {
        $response = Http::withHeaders(
            $this->getGeneralDefaultHeaders()
        )->get($this->connectionCheckUrl.'?authEventId='.$authEventId);
        $data = json_decode($response->getBody()->getContents());
        return $data[0];
    }

    public function revokeConnection($connectionId)
    {
        Http::withHeaders(
            $this->getGeneralDefaultHeaders()
        )->delete($this->connectionCheckUrl.'/'.$connectionId);
    }

    public function exchangeToken($code)
    {
        $headers = [
            'Authorization' => "Basic ".base64_encode($this->config->client_id.':'.$this->config->client_secret)
        ];
        $body = [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->config->redirect_url
        ];
        try {
            $response = Http::withHeaders($headers)->asForm()->post($this->tokenUrl, $body);
            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public function checkIfTokenIsValid()
    {
        if (!$this->config->access_token) {
            return false;
        }
        try {
            $response = Http::withHeaders(
                $this->getGeneralDefaultHeaders()
            )->get($this->connectionCheckUrl);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents());
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
        
       
        return false;
    }

    public function getAuthorizationUrl()
    {
        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->config->client_id,
            'scope' => $this->config->scope,
            'redirect_uri' => $this->config->redirect_url,
            'state' => 525489301,
        ]);
        return $this->authorizationUrl.'?'.$params;
    }

    public function getGeneralDefaultHeaders()
    {
        $config = Config::first();
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$config->access_token}",
        ];
    }

    public function getTenantDefaultHeaders($tenantId)
    {
        return array_merge($this->getGeneralDefaultHeaders(), [
            'Xero-tenant-id' => $tenantId
        ]);
    }

    public function getOrganization($tenantId)
    {
        $url = $this->baseUrl.'/Organisation';
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))->get($url);
            $data = json_decode($response->getBody()->getContents());
            return $data->Organisations[0];
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }
}
