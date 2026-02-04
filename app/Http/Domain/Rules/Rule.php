<?php

namespace App\Http\Domain\Rules;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Rewards\RewardInstruction;

final class Rule
{
    private function __construct(
        private readonly string $event_type,
        private int $points,
    ) {}

    public static function whenEventType(string $event_type): self
    {
        return new self($event_type, 0);
    }

    public function givePoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function matches(Event $event): bool
    {
        return $event->type === $this->event_type;
    }

    public function toRewardInstruction(): RewardInstruction
    {
        return new RewardInstruction($this->points);
    }
}
