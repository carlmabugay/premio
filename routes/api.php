<?php

use Illuminate\Support\Facades\Route;

Route::post('/events', fn() => response()->json(['ok' => true], 201));

