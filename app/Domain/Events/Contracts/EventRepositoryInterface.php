<?php

namespace App\Domain\Events\Contracts;

use App\Domain\Events\Entities\Event;

interface EventRepositoryInterface
{
    public function exists(Event $event): bool;

    public function save(Event $event): void;
}
