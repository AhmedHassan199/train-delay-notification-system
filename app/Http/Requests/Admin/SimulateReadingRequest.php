<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SimulateReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'exists:trips,id'],
            'distance_remaining_km' => ['required', 'numeric', 'min:0'],
            'current_speed_kmh' => ['required', 'numeric', 'min:0'],
        ];
    }
}
