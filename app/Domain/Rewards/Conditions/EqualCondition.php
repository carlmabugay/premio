<?php

namespace App\Domain\Rewards\Conditions;

use App\Domain\Events\Entities\Event;

readonly class EqualCondition implements RuleCondition
{
    public function __construct(
        private string $field,
        private int|string $value,
    ) {}

    public function isSatisfiedBy(Event $event): bool
    {
        if (! array_key_exists($this->field, $event->payload())) {
            return false;
        }

        return $this->value === $event->payload()[$this->field];
    }
}
