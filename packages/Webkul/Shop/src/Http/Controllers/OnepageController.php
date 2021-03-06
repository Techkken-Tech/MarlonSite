<?php

namespace Webkul\Shop\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Event;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\Shipping\Facades\Shipping;
use Webkul\Payment\Facades\Payment;
use Webkul\Checkout\Http\Requests\CustomerAddressForm;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use \Illuminate\Http\Request;
use Illuminate\Support\Str;
use Webkul\Checkout\Models\CartPayment;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Sales\Models\OrderComment;
use Webkul\Sales\Repositories\OrderCommentRepository;
use Webkul\Sales\Models\Order;

class OnepageController extends Controller
{
    const MINIMUM_CART_VALUE = 100;
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

     /**
     * customerRepository instance object
     *
     * @var \Webkul\Customer\Repositories\CustomerRepository
     */
    protected $customerRepository;

    protected $orderCommentRepository;

    protected $cartRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param \Webkul\Sales\Repositories\OrderCommentRepository $orderCommentRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        OrderCommentRepository $orderCommentRepository,
        CartRepository $cartRepository

    )
    {
        $this->orderRepository = $orderRepository;

        $this->customerRepository = $customerRepository;

        $this->orderCommentRepository = $orderCommentRepository;

        $this->cartRepository = $cartRepository;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
    */
    public function index()
    {
        Event::dispatch('checkout.load.index');

        if (! auth()->guard('customer')->check()
            && ! core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')) {
            return redirect()->route('customer.session.index');
        }

        if (Cart::hasError()) {
            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        if (! auth()->guard('customer')->check() && $cart->hasDownloadableItems()) {
            return redirect()->route('customer.session.index');
        }

        if (! auth()->guard('customer')->check() && ! $cart->hasGuestCheckoutItems()) {
            return redirect()->route('customer.session.index');
        }

        $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;

        if (! $cart->checkMinimumOrder()) {
            session()->flash('warning', trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));

            return redirect()->back();
        }

        Cart::collectTotals();

        return view($this->_config['view'], compact('cart'));
    }

    /**
     * Return order short summary
     *
     * @return \Illuminate\Http\Response
    */
    public function summary()
    {
        $cart = Cart::getCart();

        return response()->json([
            'html' => view('shop::checkout.total.summary', compact('cart'))->render(),
        ]);
    }

    /**
     * Saves customer address.
     *
     * @param  \Webkul\Checkout\Http\Requests\CustomerAddressForm  $request
     * @return \Illuminate\Http\Response
    */
    public function saveAddress(CustomerAddressForm $request)
    {
        $data = request()->all();

        if (! auth()->guard('customer')->check() && ! Cart::getCart()->hasGuestCheckoutItems()) {
            return response()->json(['redirect_url' => route('customer.session.index')], 403);
        }

        $data['billing']['address1'] = implode(PHP_EOL, array_filter($data['billing']['address1']));
        $data['shipping']['address1'] = implode(PHP_EOL, array_filter($data['shipping']['address1']));

        if (Cart::hasError() || ! Cart::saveCustomerAddress($data)) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        } else {
            $cart = Cart::getCart();

            Cart::collectTotals();

            if ($cart->haveStockableItems()) {
                if (! $rates = Shipping::collectRates()) {
                    return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
                } else {

                    return response()->json($rates);
                }
            } else {
                return response()->json(Payment::getSupportedPaymentMethods());
            }
        }
    }

    /**
     * Saves shipping method.
     *
     * @return \Illuminate\Http\Response
    */
    public function saveShipping()
    {
        $shippingMethod = request()->get('shipping_method');

        if (Cart::hasError() || !$shippingMethod || !Cart::saveShippingMethod($shippingMethod)) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        return response()->json(Payment::getSupportedPaymentMethods());
    }

    /**
     * Saves payment method.
     *
     * @return \Illuminate\Http\Response
    */
    public function savePayment()
    {
        $payment = request()->get('payment');

        if (Cart::hasError() || ! $payment || ! Cart::savePaymentMethod($payment)) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        return response()->json([
            'jump_to_section' => 'review',
            'html'            => view('shop::checkout.onepage.review', compact('cart'))->render(),
        ]);
    }



    private function getRate($areaName){
        $drates=core()->getAllDeliveryRates();
        foreach($drates as $drate){
            if(trim($drate->name) == trim($areaName)){
                return $drate;
            }
        }
        return null;

    }

    /**
     * Saves order.
     *
     * @return \Illuminate\Http\Response
    */
    public function saveOrder()
    {
        if (Cart::hasError()) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }
        
        $order_comment = request()->get('order_comment');

        Cart::collectTotals();

        $this->validateOrder();

        $cart = Cart::getCart();
        
        $order = Cart::prepareDataForOrder();
        

        $delivery_rate = $this->getRate($order['billing_address']['city']);
    
        if($order['shipping_method'] != "free_free")  // do not apply  minimum rate  on pickup store
        {
            if ($delivery_rate) {
                if ($cart->sub_total < $delivery_rate->minimum_cartvalue) {
                    Log::debug("Minimum delivery rate error");
                    return response()->json(['success' => false, 'message' => __('Minimum delivery rate is : Php. ').$delivery_rate->minimum_cartvalue]);
                }
            } else {
                if ($cart->sub_total < self::MINIMUM_CART_VALUE) {
                    Log::debug("Minimum delivery rate error");
                    return response()->json(['success' => false, 'message' => __('Minimum delivery rate is : Php. ').self::MINIMUM_CART_VALUE]);
                }
            }
        }
        
   
        if($order['payment']['method'] == "gcash"){
            
            
            $orderedUuid = (string) Str::orderedUuid();
            CartPayment::where('cart_id', $cart->id)->update(['temp_source' => $orderedUuid]);

            $client = new GuzzleClient();
            $grandTotal =$order['grand_total'];
            $grandTotal = number_format($grandTotal, 2, '', '');
            $base_api = Config::get('paymongo.base_api');
            
            try {
                $gcashResponse = $client->request('POST', $base_api.'/sources', [
            'body' => '{"data":{"attributes":{"amount":'.$grandTotal.',"redirect":{"success":"'. Config::get('paymongo.success_url').'/'.$orderedUuid.'","failed":"'.Config::get('paymongo.fail_url').'/'.$orderedUuid.'"},"type":"gcash","currency":"PHP"}}}',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic '.base64_encode(Config::get('paymongo.public_key')),
                'Content-Type' => 'application/json',
            ],
            ]);

                if (isset(json_decode($gcashResponse->getBody())->errors)) {
                    session()->flash('warning', json_decode($gcashResponse->getBody())->errors->detail);
                    return redirect()->route('shop.checkout.cart.index');
                }
 

                $gcashRedirectUrl = json_decode($gcashResponse->getBody())->data->attributes->redirect->checkout_url;
                CartPayment::where('cart_id', $cart->id)->update(['gcash_source_id' => json_decode($gcashResponse->getBody())->data->id]);
                if ($order_comment) {
                    $dataOrderComment = ['comment' => $order_comment, 'customer_notified' => 0 ,'cart_id' => $cart->id];
                    $this->orderCommentRepository->create($dataOrderComment);
                }

                        
                Cart::deActivateCart();

                Cart::activateCartIfSessionHasDeactivatedCartId();


                return response()->json([
                'success'      => true,
                'redirect_url' => $gcashRedirectUrl,
            ]);
            }catch(Exception $e){
                Log::debug("ERROR in Paymongo :".$e->getMessage());
                return response()->json(['success' => false, 'message' => __('Error please contact administrator.')]);
      
            }
        }


        if ($redirectUrl = Payment::getRedirectUrl($cart)) {
            return response()->json([
                'success'      => true,
                'redirect_url' => $redirectUrl,
            ]);
        }

        $order = $this->orderRepository->create($order);

        if($order_comment){
            $dataOrderComment = ['comment' => $order_comment, 'customer_notified' => 0 ,'order_id' => $order->id];
            $comment = $this->orderCommentRepository->create($dataOrderComment);

        }


        Cart::deActivateCart();

        Cart::activateCartIfSessionHasDeactivatedCartId();

        session()->flash('order', $order);


        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Order success page
     *
     * @return \Illuminate\Http\Response
    */
    public function success()
    {
        if (! $order = session('order')) {
            return redirect()->route('shop.checkout.cart.index');
        }

        return view($this->_config['view'], compact('order'));
    }

    /**
     * Order success page
     *
     * @return \Illuminate\Http\Response
    */
    public function gcashsuccess(Request $request, $slug)
    {
        //TODO check if random token match with current cart 
        $cart =  Cart::getCart();

        if($cart == null){
            //return redirect()->route('shop.checkout.cart.index');
            $cartPayment = CartPayment::where('temp_source','=', $slug)->first();

            if($cartPayment!=null){
                $order = Order::where('cart_id','=',$cartPayment->cart_id)->first();
                
                if($order!=null){
                    session()->flash('order', $order);
                    return view($this->_config['view'], compact('order'));
                }else{
                    session()->flash('warning', 'No items in cart');
                    return redirect()->route('shop.checkout.cart.index');
                }
            }else{
                session()->flash('warning', 'No items in cart');
                return redirect()->route('shop.checkout.cart.index');
            }


        }

        $cartPayment = CartPayment::where('temp_source','=', $slug)->first();
        if ($cartPayment == null) {
            session()->flash('warning', 'Token is not valid . Please try again.');
            return redirect()->route('shop.checkout.cart.index');

        }


        if ($cartPayment) {
            $order = $this->orderRepository->create(Cart::prepareDataForOrder());

            //update order_comment
            
            OrderComment::where('cart_id', $cart->id)->update(['order_id' => $order->id]);

            Cart::deActivateCart();

            Cart::activateCartIfSessionHasDeactivatedCartId();

            session()->flash('order', $order);


            return view($this->_config['view'], compact('order'));
        }else{
            session()->flash('status', 'Token is not valid . Please try again.');
            return redirect()->route('shop.checkout.cart.index');

        }
    }


        /**
     * Order success page
     *
     * @return \Illuminate\Http\Response
    */
    public function gcashfail(Request $request, $slug)
    {
        //TODO check if random token match with current cart 
        $cart =  Cart::getCart();

        if($cart == null){
            //return redirect()->route('shop.checkout.cart.index');
            $cartPayment = CartPayment::where('temp_source','=', $slug)->first();

            if($cartPayment!=null){

                $cart = $this->cartRepository->findOrFail($cartPayment->cart_id);
                $this->cartRepository->update(['is_active' => true], $cartPayment->cart_id);
                session()->put('cart', $cart);
                session()->flash('warning', 'Gcash Payment failed');
                return redirect()->route('shop.checkout.cart.index');

            }else{
                session()->flash('warning', 'No items in cart');
                return redirect()->route('shop.checkout.cart.index');
            }


        }

        $cartPayment = CartPayment::where('temp_source','=', $slug)->first();
        if ($cartPayment == null) {
            session()->flash('warning', 'Token is not valid . Please try again.');
            return redirect()->route('shop.checkout.cart.index');

        }


        return redirect()->route('shop.checkout.cart.index');
    }


    /**
     * Validate order before creation
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;

        if (! $cart->checkMinimumOrder()) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));
        }

        if ($cart->haveStockableItems() && ! $cart->shipping_address) {
            throw new \Exception(trans('Please check shipping address.'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('Please check billing address.'));
        }

        if ($cart->haveStockableItems() && ! $cart->selected_shipping_rate) {
            throw new \Exception(trans('Please specify shipping method.'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('Please specify payment method.'));
        }
    }

    /**
     * Check Customer is exist or not
     *
     * @return \Illuminate\Http\Response
     */
    public function checkExistCustomer()
    {
       $customer = $this->customerRepository->findOneWhere([
            'email' => request()->email,
       ]);

       if (! is_null($customer)) {
           return 'true';
       }

       return 'false';
    }

    /**
     * Login for checkout
     *
     * @return \Illuminate\Http\Response
     */
    public function loginForCheckout()
    {
        $this->validate(request(), [
            'email' => 'required|email'
        ]);

        if (! auth()->guard('customer')->attempt(request(['email', 'password']))) {
            return response()->json(['error' => trans('shop::app.customer.login-form.invalid-creds')]);
        }

        Cart::mergeCart();

        return response()->json(['success' => 'Login successfully']);
    }

    /**
     * To apply couponable rule requested
     *
     * @return \Illuminate\Http\Response
     */
    public function applyCoupon()
    {
        $this->validate(request(), [
            'code' => 'string|required',
        ]);

        $code = request()->input('code');

        $result = $this->coupon->apply($code);

        if ($result) {
            Cart::collectTotals();

            return response()->json([
                'success' => true,
                'message' => trans('shop::app.checkout.total.coupon-applied'),
                'result'  => $result,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => trans('shop::app.checkout.total.cannot-apply-coupon'),
                'result'  => null,
            ], 422);
        }

        return $result;
    }

    /**
     * Initiates the removal of couponable cart rule
     *
     * @return array
     */
    public function removeCoupon()
    {
        $result = $this->coupon->remove();

        if ($result) {
            Cart::collectTotals();

            return response()->json([
                'success' => true,
                'message' => trans('admin::app.promotion.status.coupon-removed'),
                'data'    => [
                    'grand_total' => core()->currency(Cart::getCart()->grand_total),
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => trans('admin::app.promotion.status.coupon-remove-failed'),
                'data'    => null,
            ], 422);
        }
    }

    /**
     * Check for minimum order.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkMinimumOrder()
    {
        $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;

        $status = Cart::checkMinimumOrder();

        return response()->json([
            'status' => ! $status ? false : true,
            'message' => ! $status ? trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]) : 'Success',
        ]);
    }

    
}