<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Application\Events\IngestEvent\IngestEventService;
use App\Http\Controllers\Controller;
use App\Http\Requests\InjestEventRequest;

class EventIngestionController extends Controller
{
    public function __invoke(
        InjestEventRequest $request,
        IngestEventService $service,
    ) {

        $result = $service->handle($request->toCommand());

        return response()->json([
            'created' => true
        ],
            $result->was_created ? 201 : 200
        );
    }
}
