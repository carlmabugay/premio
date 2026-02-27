<?php

namespace App\Domain\Rewards\Contracts;

use App\Domain\Rewards\Entities\RewardLedgerEntry;

interface RewardLedgerEntryRepositoryInterface
{
    public function save(RewardLedgerEntry $entry): void;
}
