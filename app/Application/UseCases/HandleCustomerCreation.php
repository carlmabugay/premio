<?php

namespace App\Application\UseCases;

use App\Application\DTOs\CreateCustomerDTO;
use App\Domain\Customers\Contracts\CustomerRepositoryInterface;
use App\Domain\Customers\Entities\Customer;

readonly class HandleCustomerCreation
{
    public function __construct(private CustomerRepositoryInterface $customerRepository) {}

    public function handle(CreateCustomerDto $dto): void
    {
        $customer = new Customer(
            merchant_id: $dto->merchant_id,
            external_id: $dto->external_id,
            email: $dto->email,
            meta_data: $dto->meta_data,
        );

        $this->customerRepository->save($customer);
    }
}
