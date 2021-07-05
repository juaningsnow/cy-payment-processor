<?php

namespace App\Http\Controllers;

use App\Events\ContactCreated;
use App\Events\ContactUpdated;
use App\Events\InvoiceCreated;
use App\Events\InvoiceUpdated;
use App\Models\Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookApiController extends Controller
{
    //
    public function xeroWebhooks(Request $request)
    {
        Log::info(json_encode($request->all()));
        $activeTenantId = Config::first()->xero_tenant_id;
        foreach ($request->all()['events'] as $event) {
            if ($activeTenantId == $event['tenantId']) {
                // if (($event['eventType'] == "CREATE" || $event['eventType'] == "Create") && $event['eventCategory'] == "CONTACT") {
                //     ContactCreated::dispatch(
                //         $event['resourceId']
                //     );
                // }
    
                // if (($event['eventType'] == "UPDATE" || $event['eventType'] == "Update") && $event['eventCategory'] == "CONTACT") {
                //     ContactUpdated::dispatch(
                //         $event['resourceId']
                //     );
                // }

                if (($event['eventType'] == "CREATE" || $event['eventType'] == "Create") && $event['eventCategory'] == "INVOICE") {
                    Log::info("Invoice Create");
                    InvoiceCreated::dispatch(
                        $event['resourceId']
                    );
                }
    
                if (($event['eventType'] == "UPDATE" || $event['eventType'] == "Update") && $event['eventCategory'] == "INVOICE") {
                    Log::info("Invoice Update");
                    InvoiceUpdated::dispatch(
                        $event['resourceId']
                    );
                }
            }
        }
        $response = response()->json(null, 200);
        $response->setContent(null);
        return $response;
    }

    public function pickyAssists(Request $request)
    {
        $body = [
            "number" => $request->number ? (string)$request->number : "",
            "message-in" => $request->{"message-in"} ? (string)$request->{"message-in"} : "",
            "type" => $request->type ? (string)$request->type : "",
            "application" => $request->application ? (string)$request->application : "",
            "unique-id" => $request->{"unique-id"} ? (string) $request->{"unique-id"} : "",
            "project-id" => $request->{"project-id"} ? (string) $request->{"project-id"} : "",
            "media-url" => $request->{"media-url"} ? (string)$request->{"media-url"} : "",
            "custom-variable" => $request->{"custom-variable"} ? (string)$request->{"custom-variable"} : ""
        ];
        $url = "https://prod-24.southeastasia.logic.azure.com:443/workflows/73d02f855db3486b8c17960e9164672f/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=r5BcDCSSI6FfIfG3g7JjEKZj6OxXIuBpjdAZvFwzc6A";
        $test = Http::post($url, $body);
        $response = response()->json([
            "message-out" => $request->{"message-in"},
            "delay" => "0",
            "type" => $request->type,
            "number" => $request->number
        ], 200);
        return $response;
    }
}
