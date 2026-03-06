<?php

namespace App\Domain\Customers\Contracts;

interface CustomerRepositoryInterface
{
    public function save(string $merchant_id, string $external_customer_id): void;
}
