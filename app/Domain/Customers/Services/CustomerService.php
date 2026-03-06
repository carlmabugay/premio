<?php

namespace App\Domain\Customers\Services;

use App\Domain\Customers\Contracts\CustomerRepositoryInterface;

readonly class CustomerService
{
    public function __construct(private CustomerRepositoryInterface $repository) {}

    public function save(string $merchant_id, string $external_customer_id): void
    {
        $this->repository->save($merchant_id, $external_customer_id);
    }
}
