<?php

use App\Http\Controllers\Api\V1\EventIngestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/events', EventIngestionController::class);
});
