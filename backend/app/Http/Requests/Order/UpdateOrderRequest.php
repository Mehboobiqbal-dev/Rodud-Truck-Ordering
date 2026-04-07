<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_location'   => ['sometimes', 'string', 'max:500'],
            'delivery_location' => ['sometimes', 'string', 'max:500'],
            'cargo_size'        => ['sometimes', 'string', 'max:100'],
            'cargo_weight'      => ['sometimes', 'numeric', 'min:0.01', 'max:999999.99'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'pickup_datetime'   => ['sometimes', 'date', 'after:now'],
            'delivery_datetime' => ['sometimes', 'date', 'after:pickup_datetime'],
        ];
    }
}
