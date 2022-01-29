<?php

namespace Webkul\Payment\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Checkout\Models\CartPayment;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Repositories\OrderRepository;

class GCashController extends Controller
{
    //
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

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
            elseif($type == 'payment.paid') {
                $source_id = $payload['data']['attributes']['data']['attributes']['source']['id'];
                Log::debug("payment.paid webhook", [$source_id]);

                $cart_payment = CartPayment::where('gcash_source_id', $source_id)->get();
                Log::debug("cart_payment", [$cart_payment]);

                Log::debug("cart_id", [$cart_payment[0]->cart_id]);

                $order = $this->orderRepository->where('cart_id', $cart_payment[0]->cart_id)->get();

                Log::debug("order", [$order]);

                // Update Order Status from Pending Payment to Pending.
                $order->status = "pending";
                $order->save();
            }

            return response('', 200);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response($ex->getMessage(), 500);
        }
    }
}
