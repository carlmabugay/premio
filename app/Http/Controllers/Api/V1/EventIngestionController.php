<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\CreateEventDTO;
use App\Application\UseCases\HandleEventIngestion;
use App\Http\Controllers\Controller;
use App\Http\Requests\IngestEventRequest;
use Throwable;

class EventIngestionController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(IngestEventRequest $request, HandleEventIngestion $handler)
    {
        $dto = CreateEventDTO::fromArray($request->validated());

        $result = $handler->handle($dto);

        return response()->json([
            'data' => [
                'event_id' => $result->event_id,
            ],
        ],
            $result->status_code,
        );
    }
}
