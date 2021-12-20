<?php

namespace Webkul\Shipping\Carriers;

use Config;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Facades\Shipping;
use Webkul\Checkout\Facades\Cart;

/**
 * Class Rate.
 *
 */
class FlatRate extends AbstractShipping
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'flatrate';

    /**
     * Returns rate for flatrate
     *
     * @return CartShippingRate|false
     */
    public function calculate()
    {
       
        
        if (! $this->isAvailable()) {
            return false;
        }

        $cart = Cart::getCart();

        $object = new CartShippingRate;
        
        $object->carrier = 'flatrate';
        $object->carrier_title = $this->getConfigData('title');
        $object->method = 'flatrate_flatrate';
        $object->method_title = $this->getConfigData('title');
        $object->method_description = $this->getConfigData('description');
        $object->is_calculate_tax = $this->getConfigData('is_calculate_tax');
        $_rate= $this->getRate($cart->shipping_address->city );
        if ($_rate!=null) {
            $object->price = $_rate->rate;
            $object->base_price = $_rate->rate;
            $object->method_description.=" (".$_rate->estimated_time.")";
        }else{
            return false;
        }
        


        return $object;
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

}