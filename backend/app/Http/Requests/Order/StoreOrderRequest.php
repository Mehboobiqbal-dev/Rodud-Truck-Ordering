<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_location'   => ['required', 'string', 'max:500'],
            'delivery_location' => ['required', 'string', 'max:500'],
            'cargo_size'        => ['required', 'string', 'max:100'],
            'cargo_weight'      => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'pickup_datetime'   => ['required', 'date', 'after:now'],
            'delivery_datetime' => ['required', 'date', 'after:pickup_datetime'],
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_location.required'   => 'Pickup location is required.',
            'delivery_location.required' => 'Delivery location is required.',
            'cargo_size.required'        => 'Cargo size/type is required.',
            'cargo_weight.required'      => 'Cargo weight is required.',
            'cargo_weight.min'           => 'Cargo weight must be greater than zero.',
            'pickup_datetime.required'   => 'Pickup date and time is required.',
            'pickup_datetime.after'      => 'Pickup date must be in the future.',
            'delivery_datetime.required' => 'Delivery date and time is required.',
            'delivery_datetime.after'    => 'Delivery date must be after pickup date.',
        ];
    }
}
