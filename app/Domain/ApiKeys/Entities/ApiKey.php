<?php

namespace App\Domain\ApiKeys\Entities;

readonly class ApiKey
{
    public function __construct(
        private string $merchant_id,
        private string $name,
        private string $key_hash,
        private bool $is_active,
    ) {}

    public function merchantId(): string
    {
        return $this->merchant_id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function keyHash(): string
    {
        return $this->key_hash;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
