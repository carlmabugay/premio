<?php

namespace App\Http\Domain\Rewards;

final readonly class RewardInstruction
{
    public function __construct(
        public int $points,
    ) {}

    public static function points(int $points): self
    {
        return new self($points);
    }

    public function getPoints(): int
    {
        return $this->points;
    }
}
