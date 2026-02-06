<?php

namespace App\Http\Infrastructure\Persistence\Eloquent;

use App\Http\Domain\Rewards\RewardInstruction;
use App\Http\Domain\Rewards\RewardRepository;
use App\Models\RewardLedgerEntry;
use Illuminate\Support\Str;

class EloquentRewardRepository implements RewardRepository
{
    public function issue(RewardInstruction $reward): void
    {
        // TODO: Delete Dummy Data.
        RewardLedgerEntry::create([
            'id' => Str::uuid(),
            'subject_type' => 'customer',
            'subject_id' => 1,
            'points' => $reward->getPoints(),
        ]);
    }
}
