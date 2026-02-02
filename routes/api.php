<?php

use App\Http\Controllers\EventIngestionController;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/events', EventIngestionController::class);
