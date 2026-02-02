<?php

use App\Http\Controllers\EventIngestionController;
use Illuminate\Support\Facades\Route;

Route::post('/events', EventIngestionController::class);
