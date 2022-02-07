<?php

namespace Techkken\OrderStatusUpdates\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatedOrder implements ShouldBroadcastNow
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['order'.$this->order->id];
    }

    public function broadcastAs()
    {
        
        return 'order'.$this->order->id;
    }

    public function broadcastWith()
    {
        return ['id' => $this->order->id,'status' => $this->order->status];
    }
   
}
