<?php

namespace App\Http\Controllers;

use App\Http\Application\Events\IngestEvent\IngestEventService;
use App\Http\Requests\InjestEventRequest;
use Illuminate\Http\Request;

class EventIngestionController extends Controller
{
    public function __invoke(
        InjestEventRequest $request,
        IngestEventService $service,
    ) {
        $service->handle($request->toCommand());

        return response()->json(['ok' => true], 201);
    }
}
