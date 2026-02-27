<?php

namespace App\Domain\Customers\Entities;

readonly class Customer
{
    public function __construct(
        private string $merchant_id,
        private string $external_id,
        private string $email,
        private array $meta_data,
    ) {}

    public function merchantId(): string
    {
        return $this->merchant_id;
    }

    public function externalId(): string
    {
        return $this->external_id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function metaData(): array
    {
        return $this->meta_data;
    }
}
