<?php

namespace Webkul\Payment\Payment;

class GCash extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'gcash';

    public function getRedirectUrl()
    {

    }
}