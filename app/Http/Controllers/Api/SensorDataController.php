<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSensorDataRequest;
use App\Models\SensorReading;
use Illuminate\Http\JsonResponse;

class SensorDataController extends Controller
{
    /**
     * Store sensor data from ESP32
     *
     * POST /api/sensor-data
     */
    public function store(StoreSensorDataRequest $request): JsonResponse
    {
        $reading = SensorReading::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sensor data stored successfully.',
            'data' => [
                'id' => $reading->id,
                'created_at' => $reading->created_at->toISOString(),
            ],
        ], 201);
    }
}
