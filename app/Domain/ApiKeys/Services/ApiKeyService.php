<?php

namespace App\Domain\ApiKeys\Services;

use App\Domain\ApiKeys\Contracts\ApiKeyRepositoryInterface;

readonly class ApiKeyService
{
    public function __construct(
        private ApiKeyRepositoryInterface $repository
    ) {}

    public function isKeyExists(string $key): bool
    {
        return $this->repository->exists($key);
    }
}
