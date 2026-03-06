<?php

use App\Domain\Customers\Contracts\CustomerRepositoryInterface;
use App\Domain\Customers\Services\CustomerService;
use Illuminate\Support\Str;

describe('Unit: CustomerService', function () {

    describe('Positives', function () {

        it('saves a merchant\'s customer record.', function () {

            // Arrange:
            $repo = Mockery::mock(CustomerRepositoryInterface::class);
            $merchant_id = Str::uuid()->toString();
            $external_customer_id = 'CUST-123';

            // Expectation / Assert:
            $repo->shouldReceive('save')
                ->once();

            // Act:
            $services = new CustomerService($repo);
            $services->save($merchant_id, $external_customer_id);
        });
    });

});
