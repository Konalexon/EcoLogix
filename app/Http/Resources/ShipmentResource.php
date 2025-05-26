<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_uuid' => $this->tracking_uuid,
            'tracking_number' => $this->tracking_number,
            'tracking_url' => $this->tracking_url,
            'barcode' => $this->barcode,

            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
                'icon' => $this->status->icon(),
                'is_terminal' => $this->status->isTerminal(),
            ],

            'priority' => [
                'value' => $this->priority->value,
                'label' => $this->priority->label(),
                'color' => $this->priority->color(),
            ],

            'sender' => [
                'name' => $this->sender_name,
                'company' => $this->sender_company,
                'phone' => $this->sender_phone,
                'email' => $this->sender_email,
                'address' => $this->sender_full_address,
                'city' => $this->sender_city,
                'coordinates' => $this->sender_coordinates,
            ],

            'recipient' => [
                'name' => $this->recipient_name,
                'company' => $this->recipient_company,
                'phone' => $this->recipient_phone,
                'email' => $this->recipient_email,
                'address' => $this->recipient_full_address,
                'city' => $this->recipient_city,
                'coordinates' => $this->recipient_coordinates,
            ],

            'dimensions' => [
                'weight_kg' => (float) $this->weight_kg,
                'length_cm' => (float) $this->length_cm,
                'width_cm' => (float) $this->width_cm,
                'height_cm' => (float) $this->height_cm,
                'volume_m3' => $this->volume_m3,
                'volumetric_weight_kg' => $this->volumetric_weight_kg,
                'chargeable_weight_kg' => $this->chargeable_weight_kg,
                'pieces_count' => $this->pieces_count,
            ],

            'special_handling' => [
                'is_fragile' => $this->is_fragile,
                'requires_signature' => $this->requires_signature,
                'is_hazardous' => $this->is_hazardous,
                'hazmat_class' => $this->hazmat_class,
                'requires_refrigeration' => $this->requires_refrigeration,
                'is_oversized' => $this->is_oversized,
                'keep_upright' => $this->keep_upright,
                'has_special_handling' => $this->has_special_handling,
            ],

            'delivery' => [
                'is_delayed' => $this->is_delayed,
                'delay_minutes' => $this->delay_minutes,
                'delivery_attempts' => $this->delivery_attempts,
                'max_delivery_attempts' => $this->max_delivery_attempts,
                'can_retry' => $this->canRetry(),
                'delivery_window' => $this->delivery_window_start && $this->delivery_window_end
                    ? "{$this->delivery_window_start} - {$this->delivery_window_end}"
                    : null,
                'instructions' => $this->delivery_instructions,
                'safe_place' => $this->safe_place,
            ],

            'cod' => $this->when($this->is_cod, [
                'is_cod' => $this->is_cod,
                'amount' => (float) $this->cod_amount,
                'collected' => $this->cod_collected,
            ]),

            'value' => $this->when($this->declared_value, [
                'declared_value' => (float) $this->declared_value,
                'currency' => $this->currency,
                'is_insured' => $this->is_insured,
                'insurance_value' => (float) $this->insurance_value,
            ]),

            'pricing' => $this->when($this->total_price, [
                'base_price' => (float) $this->base_price,
                'fuel_surcharge' => (float) $this->fuel_surcharge,
                'special_handling_fee' => (float) $this->special_handling_fee,
                'total_price' => (float) $this->total_price,
            ]),

            'timestamps' => [
                'created_at' => $this->created_at?->toIso8601String(),
                'estimated_pickup_at' => $this->estimated_pickup_at?->toIso8601String(),
                'picked_up_at' => $this->picked_up_at?->toIso8601String(),
                'estimated_delivery_at' => $this->estimated_delivery_at?->toIso8601String(),
                'delivered_at' => $this->delivered_at?->toIso8601String(),
            ],

            'proof_of_delivery' => $this->when($this->status->value === 'delivered', [
                'received_by' => $this->received_by_name,
                'relation' => $this->received_by_relation,
                'has_signature' => (bool) $this->recipient_signature_path,
                'has_photo' => (bool) $this->delivery_photo_path,
            ]),

            'references' => [
                'customer_reference' => $this->customer_reference,
                'external_reference' => $this->external_reference,
            ],

            // Relationships (conditional)
            'company' => new CompanyResource($this->whenLoaded('company')),
            'current_warehouse' => new WarehouseResource($this->whenLoaded('currentWarehouse')),
            'current_route' => new RouteResource($this->whenLoaded('currentRoute')),
            'assigned_driver' => new UserResource($this->whenLoaded('assignedDriver')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'history' => ShipmentHistoryResource::collection($this->whenLoaded('history')),
        ];
    }
}

// update 142 

// update 167 

// update 251 

// update 353 

// u153

// u382

// u414

// 0k2whhbk
// beytjc2