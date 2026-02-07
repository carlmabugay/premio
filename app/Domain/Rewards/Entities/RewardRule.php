<?php

namespace App\Domain\Rewards\Entities;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Conditions\GreaterThanOrEqualCondition;

readonly class RewardRule
{
    public function __construct(
        private string $id,
        private string $event_type,
        private string $reward_type,
        private int $reward_value,
        private bool $is_active,
        private string $starts_at,
        private string $ends_at,
        private ?GreaterThanOrEqualCondition $condition = null
    ) {}

    public static function create(string $id, string $event_type, string $reward_type, int $reward_value, string $starts_at, $ends_at, bool $is_active, ?GreaterThanOrEqualCondition $condition = null): self
    {
        return new self(
            id: $id,
            event_type: $event_type,
            reward_type: $reward_type,
            reward_value: $reward_value,
            is_active: $is_active,
            starts_at: $starts_at,
            ends_at: $ends_at,
            condition: $condition
        );
    }

    public function matches(Event $event): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->event_type !== $event->type()) {
            return false;
        }

        if (! $event->payload()) {
            return false;
        }

        if (!$this->isWithinDateRange($event)) {
            return false;
        }

        if (is_null($this->condition)) {
            return false;
        }

        return $this->condition->isSatisfiedBy($event);
    }

    private function isWithinDateRange(Event $event): bool
    {
        $occurred_at = $event->occurred_at();

        if ($this->starts_at && $occurred_at < $this->starts_at) {
            return false;
        }

        if ($this->ends_at && $occurred_at > $this->ends_at) {
            return false;
        }

        return true;
    }
}
