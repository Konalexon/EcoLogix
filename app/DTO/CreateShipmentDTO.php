<?php

namespace App\DTO;

use App\Enums\ShipmentPriority;
use App\Http\Requests\CreateShipmentRequest;

readonly class CreateShipmentDTO
{
    public function __construct(
        public int $companyId,
        public AddressDTO $senderAddress,
        public AddressDTO $recipientAddress,
        public DimensionsDTO $dimensions,
        public ShipmentPriority $priority,
        public ?string $contentsDescription = null,
        public ?string $specialInstructions = null,
        public bool $requiresSignature = false,
        public bool $isFragile = false,
        public bool $isHazardous = false,
        public ?string $hazmatClass = null,
        public bool $requiresRefrigeration = false,
        public ?float $minTemperature = null,
        public ?float $maxTemperature = null,
        public ?float $declaredValue = null,
        public bool $isCod = false,
        public ?float $codAmount = null,
        public ?string $deliveryWindowStart = null,
        public ?string $deliveryWindowEnd = null,
        public ?string $customerReference = null,
        public int $piecesCount = 1,
    ) {
    }

    public static function fromRequest(CreateShipmentRequest $request): self
    {
        return new self(
            companyId: $request->validated('company_id'),
            senderAddress: AddressDTO::fromArray($request->validated('sender')),
            recipientAddress: AddressDTO::fromArray($request->validated('recipient')),
            dimensions: DimensionsDTO::fromArray($request->validated('dimensions')),
            priority: ShipmentPriority::from($request->validated('priority', 'standard')),
            contentsDescription: $request->validated('contents_description'),
            specialInstructions: $request->validated('special_instructions'),
            requiresSignature: $request->validated('requires_signature', false),
            isFragile: $request->validated('is_fragile', false),
            isHazardous: $request->validated('is_hazardous', false),
            hazmatClass: $request->validated('hazmat_class'),
            requiresRefrigeration: $request->validated('requires_refrigeration', false),
            minTemperature: $request->validated('min_temperature'),
            maxTemperature: $request->validated('max_temperature'),
            declaredValue: $request->validated('declared_value'),
            isCod: $request->validated('is_cod', false),
            codAmount: $request->validated('cod_amount'),
            deliveryWindowStart: $request->validated('delivery_window_start'),
            deliveryWindowEnd: $request->validated('delivery_window_end'),
            customerReference: $request->validated('customer_reference'),
            piecesCount: $request->validated('pieces_count', 1),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: $data['company_id'],
            senderAddress: AddressDTO::fromArray($data['sender']),
            recipientAddress: AddressDTO::fromArray($data['recipient']),
            dimensions: DimensionsDTO::fromArray($data['dimensions']),
            priority: ShipmentPriority::from($data['priority'] ?? 'standard'),
            contentsDescription: $data['contents_description'] ?? null,
            specialInstructions: $data['special_instructions'] ?? null,
            requiresSignature: $data['requires_signature'] ?? false,
            isFragile: $data['is_fragile'] ?? false,
            isHazardous: $data['is_hazardous'] ?? false,
            hazmatClass: $data['hazmat_class'] ?? null,
            requiresRefrigeration: $data['requires_refrigeration'] ?? false,
            minTemperature: $data['min_temperature'] ?? null,
            maxTemperature: $data['max_temperature'] ?? null,
            declaredValue: $data['declared_value'] ?? null,
            isCod: $data['is_cod'] ?? false,
            codAmount: $data['cod_amount'] ?? null,
            deliveryWindowStart: $data['delivery_window_start'] ?? null,
            deliveryWindowEnd: $data['delivery_window_end'] ?? null,
            customerReference: $data['customer_reference'] ?? null,
            piecesCount: $data['pieces_count'] ?? 1,
        );
    }

    public function hasSpecialHandling(): bool
    {
        return $this->isFragile
            || $this->isHazardous
            || $this->requiresRefrigeration;
    }
}

// update 190 

// update 250 

// update 252 

// update 306 

// update 385 
