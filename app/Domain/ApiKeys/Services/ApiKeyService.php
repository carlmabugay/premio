<?php

namespace App\Domain\ApiKeys\Services;

use App\Domain\ApiKeys\Contracts\ApiKeyRepositoryInterface;
use App\Domain\ApiKeys\Entities\ApiKey;

class ApiKeyService
{
    public function __construct(
        private readonly ApiKeyRepositoryInterface $repository
    ) {}

    public function isKeyExists(string $key): bool
    {
        return $this->repository->exists($key);
    }

    public function fetchByApiKey(string $api_key): ApiKey
    {
        return $this->repository->fetchByApiKey($api_key);
    }
}
