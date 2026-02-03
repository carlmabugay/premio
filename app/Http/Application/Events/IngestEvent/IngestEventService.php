<?php

namespace App\Http\Application\Events\IngestEvent;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Events\EventRepository;

readonly class IngestEventService
{
    public function __construct(
        private EventRepository $eventRepository,
    ) {}

    public function handle(IngestEventCommand $command): IngestEventResult
    {
        if ($this->eventRepository->existsByExternalId(
            $command->external_id, $command->source
        )) {
            return IngestEventResult::duplicate();
        }

        $event = Event::fromCommand($command);

        $this->eventRepository->save($event);

        return IngestEventResult::created();
    }
}
