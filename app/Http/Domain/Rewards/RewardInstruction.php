<?php

namespace App\Http\Domain\Rewards;

final readonly class RewardInstruction
{
    public function __construct(
        public int $points,
    ) {}
}
