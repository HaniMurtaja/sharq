<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderUpdateStatusWidget implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $order;

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
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('orders'); // Public channel
    }

    /**
     * Data to broadcast with the event.
     *
     * @return array
     */
//    public function broadcastWith()
//    {
//        return $this->order;
//    }

    /**
     * The name of the event when broadcasted.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'updateStatus';
    }
}
