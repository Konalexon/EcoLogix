<?php

namespace App\Http\Requests;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1|max:100',
            'ids.*' => 'required|integer|exists:shipments,id',
            'status' => ['required', Rule::enum(ShipmentStatus::class)],
        ];
    }
}

// update 66 

// update 115 

// update 127 

// update 136 

// update 193 

// update 249 

// update 258 

// update 314 

// u105

// u322

// u349
