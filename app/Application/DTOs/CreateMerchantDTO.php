<?php

namespace App\Application\DTOs;

class CreateMerchantDTO
{
    public function __construct(
        public string $name,
        public string $status,
    ) {}

    public function fromArray(array $merchant): self
    {
        return new self(
            name: $merchant['name'],
            status: $merchant['status'],
        );
    }
}
