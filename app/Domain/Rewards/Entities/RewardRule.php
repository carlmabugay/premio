<?php

namespace App\Domain\Rewards\Entities;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Services\ConditionEngine;
use App\Exceptions\MalformedCondition;
use App\Exceptions\UnsupportedOperator;
use DateTimeImmutable;

class RewardRule
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $event_type,
        private readonly string $reward_type,
        private readonly int $reward_value,
        private readonly bool $is_active,
        private readonly ?DateTimeImmutable $starts_at = null,
        private readonly ?DateTimeImmutable $ends_at = null,
        private readonly ?array $conditions = [],
        public int $priority = 100,
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function eventType(): string
    {
        return $this->event_type;
    }

    public function rewardType(): string
    {
        return $this->reward_type;
    }

    public function rewardValue(): int
    {
        return $this->reward_value;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function startsAt(): ?DateTimeImmutable
    {
        return $this->starts_at;
    }

    public function endsAt(): ?DateTimeImmutable
    {
        return $this->ends_at;
    }

    public function conditions(): array
    {
        return $this->conditions;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    /**
     * @throws UnsupportedOperator|MalformedCondition
     */
    public function matches(Event $event, ConditionEngine $conditionEngine): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->event_type !== $event->type()) {
            return false;
        }

        if (empty($event->payload())) {
            return false;
        }

        if (! $this->isWithinWindow($event->occurredAt())) {
            return false;
        }

        return $conditionEngine->matches($this->conditions(), $event->payload());

    }

    public function isWithinWindow(DateTimeImmutable $occurredAt): bool
    {
        if ($this->startsAt() && $occurredAt < $this->startsAt()) {
            return false;
        }

        if ($this->endsAt() && $occurredAt > $this->endsAt()) {
            return false;
        }

        return true;
    }
}
