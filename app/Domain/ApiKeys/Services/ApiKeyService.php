<?php

namespace App\Domain\ApiKeys\Services;

use App\Domain\ApiKeys\Contracts\Read\ApiKeyReadRepositoryInterface;
use App\Domain\ApiKeys\Entities\ApiKey;

class ApiKeyService
{
    public function __construct(
        private readonly ApiKeyReadRepositoryInterface $readRepository,
    ) {}

    public function isKeyExists(string $key): bool
    {
        return $this->readRepository->exists($key);
    }

    public function fetchByApiKey(string $api_key): ApiKey
    {
        return $this->readRepository->fetchByApiKey($api_key);
    }
}
