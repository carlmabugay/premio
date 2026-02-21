<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\CreateEventDTO;
use App\Application\UseCases\HandleEventIngestion;
use App\Http\Controllers\Controller;
use App\Http\Requests\IngestEventRequest;
use App\Http\Responses\EventIngestionResponse;
use Illuminate\Http\JsonResponse;
use Throwable;

class EventIngestionController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(IngestEventRequest $request, HandleEventIngestion $handler): JsonResponse
    {
        $dto = CreateEventDTO::fromArray($request->validated());

        $result = $handler->handle($dto);

        $response = new EventIngestionResponse($request->validated(), $result);

        return $response->make();
    }
}
