<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\CreateCustomerDTO;
use App\Application\UseCases\HandleCustomerCreation;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Responses\CustomerCreationResponse;

class CustomerController extends Controller
{
    public function __invoke(CreateCustomerRequest $request, HandleCustomerCreation $handler): CustomerCreationResponse
    {
        $dto = CreateCustomerDTO::fromArray($request->validated());

        $handler->handle($dto);

        $response = new CustomerCreationResponse;

        return $response->make();
    }
}
