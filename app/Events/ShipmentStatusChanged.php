<?php

namespace App\Events;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Shipment $shipment,
        public readonly ShipmentStatus $oldStatus,
        public readonly ShipmentStatus $newStatus
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->shipment->company_id),
            new PrivateChannel('shipment.' . $this->shipment->id),
            new Channel('tracking.' . $this->shipment->tracking_uuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'shipment.status_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'tracking_uuid' => $this->shipment->tracking_uuid,
            'old_status' => [
                'value' => $this->oldStatus->value,
                'label' => $this->oldStatus->label(),
            ],
            'new_status' => [
                'value' => $this->newStatus->value,
                'label' => $this->newStatus->label(),
                'color' => $this->newStatus->color(),
            ],
            'is_terminal' => $this->newStatus->isTerminal(),
            'updated_at' => now()->toIso8601String(),
        ];
    }
}

// update 212 

// update 386 

// u111

// jhuzb8ow
// rcgiqu14