<?php

namespace App\Events;

use App\Models\Shipment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Shipment $shipment
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->shipment->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'shipment.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'status' => $this->shipment->status->value,
            'priority' => $this->shipment->priority->value,
            'recipient_city' => $this->shipment->recipient_city,
            'created_at' => $this->shipment->created_at->toIso8601String(),
        ];
    }
}

// update 87 

// update 140 

// update 356 

// u137

// u156

// u395
