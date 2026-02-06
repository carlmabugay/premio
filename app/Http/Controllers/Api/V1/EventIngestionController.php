<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Application\EventIngestion\HandleIncomingEvent;
use App\Http\Application\EventIngestion\IncomingEventDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\IngestEventRequest;

class EventIngestionController extends Controller
{
    public function __invoke(IngestEventRequest $request, HandleIncomingEvent $handler)
    {

        $dto = IncomingEventDTO::fromRequest($request->validated());

        $result = $handler->handle($dto);

        return response()->json(
            ['status' => $result->status],
            $result->httpStatus(),
        );
    }
}
