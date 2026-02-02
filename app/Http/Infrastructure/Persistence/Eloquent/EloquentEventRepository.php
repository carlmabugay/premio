<?php

namespace App\Http\Infrastructure\Persistence\Eloquent;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Events\EventRepository;
use App\Models\Event as EventModel;

class EloquentEventRepository implements EventRepository
{
    public function save(Event $event): void
    {
        EventModel::create([
            'id' => $event->id,
            'external_id' => $event->external_id,
            'source' => $event->source,
            'type' => $event->type,
            'payload' => $event->payload,
            'occurred_at' => $event->occurred_at,
        ]);
    }
}
