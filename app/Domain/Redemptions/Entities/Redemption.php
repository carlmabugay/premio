<?php

namespace App\Domain\Redemptions\Entities;

readonly class Redemption
{
    public function __construct(
        private string $merchant_id,
        private string $customer_id,
        private string $type,
        private string $external_id,
        private int $points,
        private string $status,
        private array $meta_data,
        private string $processed_at,
        private string $created_at,
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

    public function externalId(): string
    {
        return $this->external_id;
    }

    public function points(): int
    {
        return $this->points;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function metaData(): array
    {
        return $this->meta_data;
    }

    public function processedAt(): string
    {
        return $this->processed_at;
    }

    public function createdAt(): string
    {
        return $this->created_at;
    }
}
