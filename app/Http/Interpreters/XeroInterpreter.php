<?php

namespace App\Http\Interpreters;

use App\Models\Config;
use Exception;
use Illuminate\Support\Facades\Http;

class XeroInterpreter
{
    private $tokenUrl = 'https://identity.xero.com/connect/token';
    private $scope = 'offline_access accounting.transactions openid profile email accounting.contacts accounting.settings';
    private $authorizationUrl = 'https://login.xero.com/identity/connect/authorize';
    private $connectionCheckUrl = 'https://api.xero.com/connections';
    private $config;

    public function __construct()
    {
        $this->config = Config::first();
    }

    public function exchangeToken($code)
    {
        $headers = [
            // 'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "Bearer ".base64_encode($this->config->client_id.":".$code)
        ];
        $body = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->config->redirect_url
        ];
        try {
            $response = Http::withHeaders($headers)->withBody($body, 'application/x-www-form-urlencoded')->post($this->tokenUrl);
            dd($response, $response->getBody()->getContents());
        } catch (Exception $e) {
            dd($e);
            return false;
        }
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
