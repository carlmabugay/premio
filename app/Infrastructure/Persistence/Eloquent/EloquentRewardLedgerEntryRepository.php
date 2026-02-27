<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Rewards\Contracts\RewardLedgerEntryRepositoryInterface;
use App\Domain\Rewards\Entities\RewardLedgerEntry;
use App\Models\RewardLedgerEntry as EloquentRewardLedgerEntry;

class EloquentRewardLedgerEntryRepository implements RewardLedgerEntryRepositoryInterface
{
    public function save(RewardLedgerEntry $entry): void
    {
        EloquentRewardLedgerEntry::create([
            'merchant_id' => $entry->merchantId(),
            'customer_id' => $entry->customerId(),
            'type' => $entry->type(),
            'reference_type' => $entry->referenceType(),
            'reference_id' => $entry->referenceId(),
            'points' => $entry->points(),
            'created_at' => $entry->createdAt(),
        ]);
    }
}
