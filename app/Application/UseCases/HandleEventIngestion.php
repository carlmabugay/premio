<?php

namespace App\Application\UseCases;

use App\Application\DTOs\CreateEventDTO;
use App\Application\Results\IngestionResult;
use App\Domain\Events\Entities\Event;
use App\Domain\Events\Services\EventService;
use App\Exceptions\DuplicateEvent;
use Illuminate\Support\Str;

readonly class HandleEventIngestion
{
    public function __construct(
        private EventService $eventService
    ) {}

    public function handle(CreateEventDTO $dto): IngestionResult
    {
        $event = new Event(
            id: Str::uuid()->toString(),
            external_id: $dto->external_id,
            type: $dto->type,
            source: $dto->source,
            payload: $dto->payload,
            occurred_at: $dto->occurred_at,
        );

        try {

            $this->eventService->record($event);

            return IngestionResult::created($event->id());

        } catch (DuplicateEvent $e) {
            return IngestionResult::duplicate($event->id());
        }

    }
}
