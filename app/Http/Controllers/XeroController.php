<?php

namespace App\Http\Controllers;

use App\Http\Interpreters\XeroInterpreter;
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
        dd($request);
    }
}
