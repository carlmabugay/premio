<?php

namespace App\Http\Application\Events\IngestEvent;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Events\EventRepository;

readonly class IngestEventService
{
    public function __construct(
        private EventRepository $eventRepository,
    ) {}

    public function handle(IngestEventCommand $command): void
    {
        $event = Event::fromCommand($command);

        $this->eventRepository->save($event);
    }
}
