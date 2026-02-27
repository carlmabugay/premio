<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Customers\Contracts\CustomerRepositoryInterface;
use App\Domain\Customers\Entities\Customer;
use App\Models\Customer as EloquentCustomer;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function save(Customer $customer): void
    {
        EloquentCustomer::create([
            'merchant_id' => $customer->merchantId(),
            'external_id' => $customer->externalId(),
            'email' => $customer->email(),
            'meta_data' => $customer->metaData(),
        ]);
    }
}
