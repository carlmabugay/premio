<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Events\Entities\Event;
use App\Models\Event as EloquentEvent;

class EloquentEventRepository implements EventRepositoryInterface
{
    public function exists(Event $event): bool
    {
        return EloquentEvent::where('external_id', $event->externalId())
            ->where('source', $event->source())
            ->exists();
    }

    public function save(Event $event): void
    {
        EloquentEvent::create([
            'id' => $event->id(),
            'external_id' => $event->externalId(),
            'type' => $event->type(),
            'source' => $event->source(),
            'payload' => $event->payload(),
            'occurred_at' => $event->occurred_at(),
        ]);
    }
}
