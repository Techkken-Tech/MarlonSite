<?php

namespace Webkul\Payment\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Controller;

class GCashController extends Controller
{
    //
    public function GCashWebhook(Request $request)
    {
        try {
            $payload = $request->all();
            Log::debug($payload);
            $type = $payload['data']['attributes']['type'];

            if ($type == 'source.chargeable') {
                $amount = $payload['data']['attributes']['data']['attributes']['amount'];
                $id = $payload['data']['attributes']['data']['id'];
                $description = "GCash Payment Description";
                $fields = array("data" => array("attributes" => array("amount" => $amount, "source" => array("id" => $id, "type" => "source"), "currency" => "PHP", "description" => $description)));
                $jsonFields = json_encode($fields);


                $client = new \GuzzleHttp\Client();

                Log::debug(env('GCASH_SECRET_KEY'));

                $response = $client->request('POST', 'https://api.paymongo.com/v1/payments', [
                    'body' => $jsonFields,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Basic ' . base64_encode(env('GCASH_SECRET_KEY')),
                        'Content-Type' => 'application/json',
                    ],
                ]);

                Log::debug($response->getBody());

                // Update Order

            }
            return response('', 200);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response($ex->getMessage(), 500);
        }
    }
}
