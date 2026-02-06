<?php

namespace App\Http\Application\EventIngestion;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Events\EventRepository;
use Illuminate\Support\Str;

final readonly class HandleIncomingEvent
{
    public function __construct(
        private EventRepository $repository,
    ) {}

    public function handle(IncomingEventDTO $dto): IngestionResult
    {
        if ($this->repository->existsByExternalId($dto->external_id, $dto->source)) {
            return IngestionResult::duplicate();
        }

        $event = Event::fromPrimitives(
            id: Str::uuid()->toString(),
            external_id: $dto->external_id,
            type: $dto->type,
            source: $dto->source,
            occurred_at: $dto->occurred_at,
            payload: $dto->payload,
        );

        $this->repository->save($event);

        return IngestionResult::created();
    }
}
