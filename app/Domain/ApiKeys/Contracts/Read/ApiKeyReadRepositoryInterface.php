<?php

namespace App\Domain\ApiKeys\Contracts\Read;

use App\Domain\ApiKeys\Entities\ApiKey;

interface ApiKeyReadRepositoryInterface
{
    public function exists(string $key): bool;

    public function fetchByApiKey(string $key): ApiKey;
}
