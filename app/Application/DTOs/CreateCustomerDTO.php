<?php

namespace App\Application\DTOs;

class CreateCustomerDTO
{
    public function __construct(
        public string $merchant_id,
        public string $external_id,
        public string $email,
        public array $meta_data,
    ) {}

    public function fromArray(array $customer): self
    {
        return new self(
            merchant_id: $customer['merchant_id'],
            external_id: $customer['external_id'],
            email: $customer['email'],
            meta_data: $customer['meta_data'],
        );
    }
}
