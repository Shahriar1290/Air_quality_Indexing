<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSensorDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temperature' => ['required', 'numeric', 'min:-40', 'max:80'],
            'humidity' => ['required', 'numeric', 'min:0', 'max:100'],
            'mq2' => ['required', 'integer', 'min:0', 'max:4095'],
            'mq5' => ['required', 'integer', 'min:0', 'max:4095'],
            'dust' => ['required', 'integer', 'min:0', 'max:1000'],
            'estimated_aqi' => ['required', 'integer', 'min:0', 'max:500'],
            'gas_status' => ['required', 'string', 'in:SAFE,DANGER'],
            'air_status' => ['required', 'string', 'in:Good,Moderate,Unhealthy,Hazardous'],
        ];
    }

    public function messages(): array
    {
        return [
            'temperature.required' => 'Temperature reading is required.',
            'temperature.numeric' => 'Temperature must be a valid number.',
            'humidity.required' => 'Humidity reading is required.',
            'mq2.required' => 'MQ2 sensor value is required.',
            'mq5.required' => 'MQ5 sensor value is required.',
            'dust.required' => 'Dust sensor value is required.',
            'estimated_aqi.required' => 'AQI value is required.',
            'gas_status.in' => 'Gas status must be SAFE or DANGER.',
            'air_status.in' => 'Air status must be Good, Moderate, Unhealthy, or Hazardous.',
        ];
    }
}
