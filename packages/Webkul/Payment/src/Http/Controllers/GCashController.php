<?php

namespace Webkul\Payment\Http\Controllers;

use Barryvdh\Debugbar\Twig\Extension\Debug;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Checkout\Models\CartPayment;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\Config;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Sales\Models\OrderComment;
use Illuminate\Support\Arr;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Product\Repositories\ProductRepository;

class GCashController extends Controller
{
    //
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    protected $cartRepository;

    protected $customerRepository;

    protected $productRepository;
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        CartRepository $cartRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
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

                Log::debug("paymongo secret debug:".Config::get('paymongo.secret_key'));
                $response = $client->request('POST', 'https://api.paymongo.com/v1/payments', [
                    'body' => $jsonFields,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Basic ' . base64_encode(Config::get('paymongo.secret_key')),
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

                if(isset($cart_payment[0]->cart_id)){
                    $cart = $this->cartRepository->findOrFail($cart_payment[0]->cart_id);


                    $order = $this->orderRepository->create($this->cartGcashPrepareDataForOrder($cart));
                    //update order_comment
    
                    OrderComment::where('cart_id', $cart->id)->update(['order_id' => $order->id]);
    
                    //Deactivate cart
                    $this->cartRepository->update(['is_active' => false], $order->cart_id);
    
    
                    $order_update = Order::findOrFail($order->id);
                    // Update Order Status from Pending Payment to Pending.
                    $order_update->status = "pending";
                    $order_update->save();  
                }

                
            }
            elseif($type == 'payment.failed') {
                $source_id = $payload['data']['attributes']['data']['attributes']['source']['id'];
                Log::debug("payment.failed webhook", [$source_id]);

                $cart_payment = CartPayment::where('gcash_source_id', $source_id)->get();
                Log::debug("cart_payment", [$cart_payment]);

                Log::debug("cart_id", [$cart_payment[0]->cart_id]);


              
                if(isset($cart_payment[0]->cart_id)){
                    $this->cartRepository->update(['is_active' => true], $cart_payment[0]->cart_id);
                }


            }
            

            return response('', 200);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response($ex->getMessage(), 500);
        }
    }

    
    /**
     * Prepare data for order.
     *
     * @return array
     */
    private function cartGcashPrepareDataForOrder($cart): array
    {
        $data = $this->GcashCarttoArray($cart);


        if(!empty($data['customer_id'])){
            $_customer =  $this->customerRepository->findOrFail($data['customer_id']);
        }else{
            $_customer = null;
        }
       
        $finalData = [
            'cart_id'               => $cart->id,
            'customer_id'           => $data['customer_id'],
            'is_guest'              => $data['is_guest'],
            'customer_email'        => $data['customer_email'],
            'customer_first_name'   => $data['customer_first_name'],
            'customer_last_name'    => $data['customer_last_name'],
            'customer'              => $_customer,
            'total_item_count'      => $data['items_count'],
            'total_qty_ordered'     => $data['items_qty'],
            'base_currency_code'    => $data['base_currency_code'],
            'channel_currency_code' => $data['channel_currency_code'],
            'order_currency_code'   => $data['cart_currency_code'],
            'grand_total'           => $data['grand_total'],
            'base_grand_total'      => $data['base_grand_total'],
            'sub_total'             => $data['sub_total'],
            'base_sub_total'        => $data['base_sub_total'],
            'tax_amount'            => $data['tax_total'],
            'base_tax_amount'       => $data['base_tax_total'],
            'coupon_code'           => $data['coupon_code'],
            'applied_cart_rule_ids' => $data['applied_cart_rule_ids'],
            'discount_amount'       => $data['discount_amount'],
            'base_discount_amount'  => $data['base_discount_amount'],
            'billing_address'       => Arr::except($data['billing_address'], ['id', 'cart_id']),
            'payment'               => Arr::except($data['payment'], ['id', 'cart_id']),
            'channel'               => core()->getCurrentChannel(),
        ];

        if ($cart->haveStockableItems()) {
            $finalData = array_merge($finalData, [
                'shipping_method'               => $data['selected_shipping_rate']['method'],
                'shipping_title'                => $data['selected_shipping_rate']['carrier_title'] . ' - ' . $data['selected_shipping_rate']['method_title'],
                'shipping_description'          => $data['selected_shipping_rate']['method_description'],
                'shipping_amount'               => $data['selected_shipping_rate']['price'],
                'base_shipping_amount'          => $data['selected_shipping_rate']['base_price'],
                'shipping_address'              => Arr::except($data['shipping_address'], ['id', 'cart_id']),
                'shipping_discount_amount'      => $data['selected_shipping_rate']['discount_amount'],
                'base_shipping_discount_amount' => $data['selected_shipping_rate']['base_discount_amount'],
            ]);
        }

        foreach ($data['items'] as $item) {
            $finalData['items'][] = $this->gcashCartPrepareDataForOrderItem($item);
        }

        return $finalData;
    }

    

    /**
     * Returns cart details in array.
     *
     * @return array
     */
    public function GcashCarttoArray($cart)
    {

        $data = $cart->toArray();

        $data['billing_address'] = $cart->billing_address->toArray();

        if ($cart->haveStockableItems()) {
            $data['shipping_address'] = $cart->shipping_address->toArray();

            $data['selected_shipping_rate'] = $cart->selected_shipping_rate ? $cart->selected_shipping_rate->toArray() : 0.0;
        }

        $data['payment'] = $cart->payment->toArray();

        $data['items'] = $cart->items->toArray();

        return $data;
    }

    
    /**
     * Prepares data for order item.
     *
     * @param  array  $data
     * @return array
     */
    public function gcashCartPrepareDataForOrderItem($data): array
    {
        $locale = ['locale' => core()->getCurrentLocale()->code];

        $finalData = [
            'product'              => $this->productRepository->find($data['product_id']),
            'sku'                  => $data['sku'],
            'type'                 => $data['type'],
            'name'                 => $data['name'],
            'weight'               => $data['weight'],
            'total_weight'         => $data['total_weight'],
            'qty_ordered'          => $data['quantity'],
            'price'                => $data['price'],
            'base_price'           => $data['base_price'],
            'total'                => $data['total'],
            'base_total'           => $data['base_total'],
            'tax_percent'          => $data['tax_percent'],
            'tax_amount'           => $data['tax_amount'],
            'base_tax_amount'      => $data['base_tax_amount'],
            'discount_percent'     => $data['discount_percent'],
            'discount_amount'      => $data['discount_amount'],
            'base_discount_amount' => $data['base_discount_amount'],
            'additional'           => is_array($data['additional']) ? array_merge($data['additional'],$locale) : $locale,
        ];

        if (isset($data['children']) && $data['children']) {
            foreach ($data['children'] as $child) {
                $child['quantity'] = $child['quantity'] ? $child['quantity'] * $data['quantity'] : $child['quantity'];

                $finalData['children'][] = $this->gcashCartPrepareDataForOrderItem($child);
            }
        }

        return $finalData;
    }

}
