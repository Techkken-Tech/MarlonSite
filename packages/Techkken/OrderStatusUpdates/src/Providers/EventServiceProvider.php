<?php

namespace Techkken\OrderStatusUpdates\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('sales.order.process', 'Techkken\OrderStatusUpdates\Listeners\UpdateCustomer@handle');
    }
}