<?php

namespace App\Domain\Rewards\Entities;

use App\Domain\Events\Entities\Event;

readonly class RewardRule
{
    public function __construct(
        private int $id,
        private string $event_type,
        private string $reward_type,
        private int $reward_value,
        private bool $is_active,
        private ?string $starts_at = null,
        private ?string $ends_at = null,
        private ?array $conditions = []
    ) {}

    public static function create(string $id, string $event_type, string $reward_type, int $reward_value, bool $is_active, ?string $starts_at = null, ?string $ends_at = null, ?array $conditions = []): self
    {
        return new self(
            id: $id,
            event_type: $event_type,
            reward_type: $reward_type,
            reward_value: $reward_value,
            is_active: $is_active,
            starts_at: $starts_at,
            ends_at: $ends_at,
            conditions: $conditions
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

        if (! $this->isWithinDateRange($event)) {
            return false;
        }

        if (empty($this->conditions)) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if (! $condition->isSatisfiedBy($event)) {
                return false;
            }
        }

        return true;
    }

    private function isWithinDateRange(Event $event): bool
    {
        $occurred_at = $event->occurred_at();

        if (! $this->starts_at || ! $this->ends_at) {
            return true;
        }

        if ($occurred_at < $this->starts_at) {
            return false;
        }

        if ($occurred_at > $this->ends_at) {
            return false;
        }

        return true;
    }
}
