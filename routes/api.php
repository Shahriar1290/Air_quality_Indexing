<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;

Route::post('/sensor-data', [SensorDataController::class, 'store']);
