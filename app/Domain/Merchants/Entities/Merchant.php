<?php

namespace App\Domain\Merchants\Entities;

class Merchant
{
    public function __construct(
        public string $name,
        public string $status,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function status(): string
    {
        return $this->status;
    }
}
