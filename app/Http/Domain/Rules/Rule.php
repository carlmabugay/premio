<?php

namespace App\Http\Domain\Rules;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Rewards\RewardInstruction;
use App\Http\Domain\Rules\Conditions\PayloadAtLeast;

final class Rule
{
    private array $conditions = [];

    private array $rewardInstructions = [];

    private bool $enabled = true;

    private function __construct(
        private readonly string $event_type,
    ) {}

    public static function whenEventType(string $event_type): self
    {
        return new self($event_type);
    }

    public function whenPayloadAtLeast(string $key, int|float $value): self
    {
        $this->conditions[] = new PayloadAtLeast($key, $value);

        return $this;
    }

    public function givePoints(int $points): self
    {
        $this->rewardInstructions[] = RewardInstruction::points($points);

        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    public function matches(Event $event): bool
    {
        if (! $this->enabled) {
            return false;
        }

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

    public function rewards(): array
    {
        return $this->rewardInstructions;
    }
}
