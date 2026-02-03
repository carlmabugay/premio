<?php

namespace App\Http\Domain\Events;

interface EventRepository
{
    public function existsByExternalId(string $external_id, string $source): bool;

    public function save(Event $event): void;
}
