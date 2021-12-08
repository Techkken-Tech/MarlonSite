<?php
return [
    'cashondelivery'  => [
        'code'        => 'cashondelivery',
        'title'       => 'Cash On Delivery',
        'description' => 'Cash On Delivery',
        'class'       => 'Webkul\Payment\Payment\CashOnDelivery',
        'active'      => true,
        'sort'        => 1,
    ],

    'moneytransfer'   => [
        'code'        => 'moneytransfer',
        'title'       => 'Money Transfer',
        'description' => 'Money Transfer',
        'class'       => 'Webkul\Payment\Payment\MoneyTransfer',
        'active'      => true,
        'sort'        => 2,
    ],

    'gcash'   => [
        'code'        => 'gcash',
        'title'       => 'GCash',
        'description' => 'GCash',
        'class'       => 'Webkul\Payment\Payment\GCash',
        'active'      => true,
        'sort'        => 3,
    ]
];