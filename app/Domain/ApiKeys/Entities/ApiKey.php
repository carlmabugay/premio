<?php

namespace App\Domain\ApiKeys\Entities;

class ApiKey
{
    public function __construct(
        public string $merchant_id,
        public string $name,
        public string $key_hash,
        public bool $is_active,
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
