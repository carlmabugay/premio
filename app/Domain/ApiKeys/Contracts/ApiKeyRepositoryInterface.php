<?php

namespace App\Domain\ApiKeys\Contracts;

use App\Domain\ApiKeys\Entities\ApiKey;

interface ApiKeyRepositoryInterface
{
    public function save(ApiKey $apiKey): void;
}
