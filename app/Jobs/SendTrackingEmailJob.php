<?php

namespace App\Jobs;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTrackingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300;

    public function __construct(
        public readonly Shipment $shipment,
        public readonly string $type = 'status_update',
    ) {
    }

    public function handle(): void
    {
        $email = $this->shipment->recipient_email;

        if (!$email) {
            return;
        }

        $mailClass = match ($this->type) {
            'created' => \App\Mail\ShipmentCreatedMail::class,
            'delivered' => \App\Mail\ShipmentDeliveredMail::class,
            'failed_delivery' => \App\Mail\DeliveryFailedMail::class,
            default => \App\Mail\TrackingUpdateMail::class,
        };

        Mail::to($email)->send(new $mailClass($this->shipment));
    }
}

// update 114 

// update 124 

// update 140 

// update 287 

// u171

// u298

// 6jezdpxe