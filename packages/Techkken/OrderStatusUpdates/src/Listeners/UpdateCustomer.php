<?php
namespace Techkken\OrderStatusUpdates\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\Log;
/**
 * Generate Invoice Event handler
 *
 */
class UpdateCustomer
{
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Create the event listener.
     *
     * @param  Webkul\Sales\Repositories\OrderRepository $orderRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository
        )
        {
            $this->orderRepository = $orderRepository;
        }

    /**
     * Generate a new invoice.
     *
     * @param  object  $order
     * @return void
     */
    public function handle($order)
    {
        Log::info('Handle Order: '.$order->id);
        if($order){
            event(new \Techkken\OrderStatusUpdates\Events\UpdatedOrder($order));
        }           
         
    }

}