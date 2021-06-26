<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
use App\Models\Config;
use Illuminate\Http\Request;

class XeroController extends Controller
{
    public function status()
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $connected = $xeroInterpreter->checkIfTokenIsValid();
        $authUrl = $xeroInterpreter->getAuthorizationUrl();
        return view('xero', [
           'authUrl' => $authUrl, 'connected' => $connected, 'title' => 'Xero API Connection Status'
        ]);
    }

    public function callback(Request $request)
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $code = $request->get('code');
        $accessToken = $request->get('access_token');
        if($accessToken){
            $config = Config::first();
            $config->access_token = $request->get('access_token');
            $config->refresh_token = $request->get('refresh_token');
            $config->save();
            return redirect()->route('xero_status');
        }
        if($code){
            $xeroInterpreter->exchangeToken($code);
        }
    }
}
