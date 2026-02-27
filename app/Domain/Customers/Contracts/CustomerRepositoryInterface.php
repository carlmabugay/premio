<?php

namespace App\Domain\Customers\Contracts;

use App\Domain\Customers\Entities\Customer;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): void;
}
