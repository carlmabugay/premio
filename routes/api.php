<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Event;

Route::post('/events', function (Request $request) {

    $event = new Event;

    $event->event_type = $request->type;
    $event->external_event_id = 'evt_123';
    $event->source = $request->source;
    $event->payload = json_encode($request->payload);
    $event->save();

    return response()->json(['ok' => true], 201);
});

