<?php

namespace App\Domain\Events\Services;

use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Events\Entities\Event;
use App\Exceptions\DuplicateEvent;

readonly class EventService
{
    public function __construct(
        private EventRepositoryInterface $eventRepository
    ) {}

    /**
     * @throws DuplicateEvent
     */
    public function record(Event $event): void
    {
        if ($this->eventRepository->exists($event)) {
            throw new DuplicateEvent;
        }

        $this->eventRepository->save($event);
    }
}
