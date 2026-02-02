<?php

namespace App\Http\Domain\Events;

interface EventRepository
{
    public function save(Event $event): void;
}
