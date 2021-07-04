<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyXeroToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->getContent()) {
            $response = response()->json(null, 401);
            $response->setContent(null);
            return $response;
        }
        
        $signatureKey = base64_encode(
            hash_hmac('sha256', $request->getContent(), config('xero.webhook_key'), true)
        );
        
        if (!hash_equals($signatureKey, $request->headers->get('x-xero-signature'))) {
            $response = response()->json(null, 401);
            $response->setContent(null);
            return $response;
        }

        return $next($request);
    }
}
