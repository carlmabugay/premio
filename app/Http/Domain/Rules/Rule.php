<?php

namespace App\Http\Domain\Rules;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Rewards\RewardInstruction;
use App\Http\Domain\Rules\Conditions\PayloadAtLeast;

final class Rule
{
    private array $conditions = [];

    private function __construct(
        private readonly string $event_type,
        private int $points,
    ) {}

    public static function whenEventType(string $event_type): self
    {
        return new self($event_type, 0);
    }

    public function whenPayloadAtLeast(string $key, int|float $value): self
    {
        $this->conditions[] = new PayloadAtLeast($key, $value);

        return $this;
    }

    public function givePoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function matches(Event $event): bool
    {
        if ($event->type !== $this->event_type) {
            return false;
        }

        foreach ($this->conditions as $condition) {
            if (! $condition->isSatisfiedBy($event)) {
                return false;
            }
        }

        return true;
    }

    public function toRewardInstruction(): RewardInstruction
    {
        return new RewardInstruction($this->points);
    }
}
