<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SensorReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'distance_remaining_km' => ['required', 'numeric', 'min:0'],
            'current_speed_kmh' => ['required', 'numeric', 'min:0'],
        ];
    }
}
