<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Customers\Contracts\CustomerRepositoryInterface;
use App\Models\Customer as EloquentCustomer;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function save(string $merchant_id, string $external_customer_id): void
    {
        EloquentCustomer::firstOrCreate(
            ['merchant_id' => $merchant_id],
            ['external_customer_id' => $external_customer_id]
        );
    }
}
