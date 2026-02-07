<?php

namespace App\Domain\Rewards\Conditions;

use App\Domain\Events\Entities\Event;

interface RuleCondition
{
    public function isSatisfiedBy(Event $event): bool;
}
