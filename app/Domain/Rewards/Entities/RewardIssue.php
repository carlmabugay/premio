<?php

namespace App\Domain\Rewards\Entities;

class RewardIssue
{
    public function __construct(
        private readonly string $event_id,
        private readonly int $reward_rule_id,
        private readonly string $reward_type,
        private readonly float $reward_value,
    ) {}

    public function eventId(): string
    {
        return $this->event_id;
    }

    public function rewardRuleId(): int
    {
        return $this->reward_rule_id;
    }

    public function rewardType(): string
    {
        return $this->reward_type;
    }

    public function rewardValue(): float
    {
        return $this->reward_value;
    }
}
