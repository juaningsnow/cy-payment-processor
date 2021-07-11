<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
use App\Models\Account;
use App\Models\Config;
use Illuminate\Http\Request;

class XeroController extends Controller
{
    public function status()
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $tokenIsValid = $xeroInterpreter->checkIfTokenIsValid();
        $companyIsConnected = auth()->user()->company->isXeroConnected();
        $connected = $tokenIsValid && $companyIsConnected ? true : false;
        $authUrl = $xeroInterpreter->getAuthorizationUrl();
        return view('xero', [
           'authUrl' => $authUrl, 'connected' => $connected, 'title' => 'Xero API Connection Status'
        ]);
    }

    public function callback(Request $request)
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $code = $request->get('code');
        if ($code) {
            $response = $xeroInterpreter->exchangeToken($code);
            $config = Config::first();
            $config->access_token = $response->access_token;
            $config->refresh_token = $response->refresh_token;
            $config->save();
            $tokenParts = explode(".", $response->access_token);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);
            $company = auth()->user()->company;
            $company->auth_event_id = $jwtPayload->authentication_event_id;
            $tenantDetails = $xeroInterpreter->getTenantConnection($jwtPayload->authentication_event_id);
            $company->xero_connection_id = $tenantDetails->id;
            $company->xero_tenant_id = $tenantDetails->tenantId;
            $company->save();
            if (Account::where('company_id', $company->id)->count() < 1) {
                $xeroInterpreter->seedAccounts($company);
            }
            return redirect()->route('xero_status');
        }
    }
}
