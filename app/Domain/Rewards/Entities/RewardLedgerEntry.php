<?php

namespace App\Domain\Rewards\Entities;

class RewardLedgerEntry
{
    public function __construct(
        private readonly string $merchant_id,
        private readonly string $customer_id,
        private readonly string $type,
        private readonly string $reference_type,
        private readonly string $reference_id,
        private float $points,
        private readonly string $created_at,
    ) {}

    public function merchantId(): string
    {
        return $this->merchant_id;
    }

    public function customerId(): string
    {
        return $this->customer_id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function referenceType(): string
    {
        return $this->reference_type;
    }

    public function referenceId(): string
    {
        return $this->reference_id;
    }

    public function points(): float
    {
        return $this->points;
    }

    public function createdAt(): string
    {
        return $this->created_at;
    }
}
