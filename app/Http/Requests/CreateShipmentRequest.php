<?php

namespace App\Http\Requests;

use App\Enums\ShipmentPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'priority' => ['nullable', Rule::enum(ShipmentPriority::class)],

            // Sender
            'sender.name' => 'required|string|max:255',
            'sender.company' => 'nullable|string|max:255',
            'sender.email' => 'nullable|email|max:255',
            'sender.phone' => 'required|string|max:20',
            'sender.street' => 'required|string|max:255',
            'sender.building' => 'required|string|max:20',
            'sender.apartment' => 'nullable|string|max:20',
            'sender.city' => 'required|string|max:100',
            'sender.postal_code' => 'required|string|max:10',
            'sender.country' => 'nullable|string|max:2',
            'sender.latitude' => 'nullable|numeric|between:-90,90',
            'sender.longitude' => 'nullable|numeric|between:-180,180',

            // Recipient
            'recipient.name' => 'required|string|max:255',
            'recipient.company' => 'nullable|string|max:255',
            'recipient.email' => 'nullable|email|max:255',
            'recipient.phone' => 'required|string|max:20',
            'recipient.street' => 'required|string|max:255',
            'recipient.building' => 'required|string|max:20',
            'recipient.apartment' => 'nullable|string|max:20',
            'recipient.city' => 'required|string|max:100',
            'recipient.postal_code' => 'required|string|max:10',
            'recipient.country' => 'nullable|string|max:2',
            'recipient.latitude' => 'nullable|numeric|between:-90,90',
            'recipient.longitude' => 'nullable|numeric|between:-180,180',

            // Dimensions
            'dimensions.weight_kg' => 'required|numeric|min:0.1|max:2000',
            'dimensions.length_cm' => 'required|numeric|min:1|max:400',
            'dimensions.width_cm' => 'required|numeric|min:1|max:200',
            'dimensions.height_cm' => 'required|numeric|min:1|max:200',
            'pieces_count' => 'nullable|integer|min:1|max:100',

            // Special handling
            'requires_signature' => 'nullable|boolean',
            'is_fragile' => 'nullable|boolean',
            'is_hazardous' => 'nullable|boolean',
            'hazmat_class' => 'nullable|string|max:10|required_if:is_hazardous,true',
            'requires_refrigeration' => 'nullable|boolean',
            'min_temperature' => 'nullable|numeric|min:-40|max:20|required_if:requires_refrigeration,true',
            'max_temperature' => 'nullable|numeric|min:-40|max:20|required_if:requires_refrigeration,true|gte:min_temperature',

            // Value
            'declared_value' => 'nullable|numeric|min:0',
            'is_cod' => 'nullable|boolean',
            'cod_amount' => 'nullable|numeric|min:0|required_if:is_cod,true',

            // Delivery
            'delivery_window_start' => 'nullable|date_format:H:i',
            'delivery_window_end' => 'nullable|date_format:H:i|after:delivery_window_start',
            'special_instructions' => 'nullable|string|max:1000',
            'contents_description' => 'nullable|string|max:500',

            // References
            'customer_reference' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'sender.name.required' => 'Nazwa nadawcy jest wymagana',
            'sender.phone.required' => 'Telefon nadawcy jest wymagany',
            'recipient.name.required' => 'Nazwa odbiorcy jest wymagana',
            'recipient.phone.required' => 'Telefon odbiorcy jest wymagany',
            'dimensions.weight_kg.required' => 'Waga przesyłki jest wymagana',
            'dimensions.weight_kg.max' => 'Maksymalna waga to 2000 kg',
            'hazmat_class.required_if' => 'Klasa ADR jest wymagana dla przesyłek niebezpiecznych',
            'cod_amount.required_if' => 'Kwota pobrania jest wymagana',
        ];
    }
}

// u235

// u316

// ue44h5s4