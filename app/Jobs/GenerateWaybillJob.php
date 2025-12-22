<?php

namespace App\Jobs;

use App\Models\Shipment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateWaybillJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly Shipment $shipment,
        public readonly ?User $requestedBy = null,
    ) {
    }

    public function handle(): void
    {
        $pdf = Pdf::loadView('pdf.waybill', [
            'shipment' => $this->shipment,
            'company' => $this->shipment->company,
            'barcode' => $this->generateBarcode(),
        ]);

        $path = "waybills/{$this->shipment->tracking_number}.pdf";

        Storage::put($path, $pdf->output());

        $this->shipment->update(['waybill_path' => $path]);
    }

    private function generateBarcode(): string
    {
        // Generate Code128 barcode as base64 image
        return 'data:image/png;base64,' . base64_encode(
            (new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode(
                $this->shipment->barcode ?? $this->shipment->tracking_number,
                \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128
            )
        );
    }
}

// update 72 

// update 245 

// update 287 

// update 403 

// u93

// u109

// u140
