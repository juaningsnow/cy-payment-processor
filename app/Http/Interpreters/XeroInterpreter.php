<?php

namespace App\Http\Interpreters;

use App\Models\Config;
use App\Models\Supplier;
use BaseCode\Common\Exceptions\GeneralApiException;
use Exception;
use Illuminate\Support\Facades\Http;

class XeroInterpreter
{
    private $tokenUrl = 'https://identity.xero.com/connect/token';
    private $authorizationUrl = 'https://login.xero.com/identity/connect/authorize';
    private $connectionCheckUrl = 'https://api.xero.com/connections';
    private $baseUrl = 'https://api.xero.com/api.xro/2.0';
    private $config;

    public function __construct()
    {
        $this->config = Config::first();
    }

    public function createContact(Supplier $supplier)
    {
        $body = [
            'Name' => $supplier->name,
            'EmailAddress' => $supplier->email,
            'BankAccountDetails' => $supplier->account_number
        ];

        try {
            $response = Http::withHeaders($this->getDefaultHeaders())->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/Contacts');
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
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
            $data = json_decode($response->getBody()->getContents());
            $config = Config::first();
            $config->access_token = $data->access_token;
            $config->refresh_token = $data->refresh_token;
            $config->save();
            return redirect()->route('xero_status');
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
                $this->getDefaultHeaders()
            )->get($this->connectionCheckUrl);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents());
                $config = Config::first();
                $config->xero_tenant_id = $data[0]->tenantId;
                $config->save();
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

    public function getDefaultHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$this->config->access_token}",
        ];
    }
}
