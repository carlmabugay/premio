<?php

namespace App\Domain\Rewards\Conditions;

use App\Domain\Events\Entities\Event;

readonly class GreaterThanOrEqualCondition implements RuleCondition
{
    public function __construct(
        private string $field,
        private int $value,
    ) {}

    public function isSatisfiedBy(Event $event): bool
    {
        if (! array_key_exists($this->field, $event->payload()) && ! is_numeric($event)) {
            return false;
        }

        return $event->payload()[$this->field] >= $this->value;
    }
}
