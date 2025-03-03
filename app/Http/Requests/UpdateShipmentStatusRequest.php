<?php

namespace App\Http\Requests;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(ShipmentStatus::class)],
            'metadata' => 'nullable|array',
            'metadata.notes' => 'nullable|string|max:500',
            'metadata.reason' => 'nullable|string|max:255',
            'metadata.location' => 'nullable|string|max:255',
        ];
    }
}

// update 396 

// u241
