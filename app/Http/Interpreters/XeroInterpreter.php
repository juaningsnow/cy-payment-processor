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
        // try {
        //     $response = 
        // }
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
