<?php

namespace App\Http\Domain\Rules;

use App\Http\Domain\Events\Event;

interface Condition
{
    public function isSatisfiedBy(Event $event): bool;
}
