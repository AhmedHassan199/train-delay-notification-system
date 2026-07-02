<?php

use App\Http\Controllers\Api\SensorController;
use Illuminate\Support\Facades\Route;

Route::middleware('sensor.token')->group(function () {
    Route::post('/trips/{trip}/reading', [SensorController::class, 'reading']);
});
