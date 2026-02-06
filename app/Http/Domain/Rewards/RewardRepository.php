<?php

namespace App\Http\Domain\Rewards;

interface RewardRepository
{
    public function issue(RewardInstruction $reward): void;
}
