<?php

namespace App\Http\Domain\Rules\Conditions;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Rules\Condition;

final class PayloadAtLeast implements Condition
{
    public function __construct(
        private string $key,
        private int|string $minimum,
    ) {}

    public function isSatisfiedBy(Event $event): bool
    {
        if (! array_key_exists($this->key, $event->payload)) {
            return false;
        }

        return $event->payload[$this->key] >= $this->minimum;
    }
}
