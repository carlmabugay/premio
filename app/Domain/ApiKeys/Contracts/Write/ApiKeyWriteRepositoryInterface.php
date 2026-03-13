<?php

namespace App\Domain\ApiKeys\Contracts\Write;

use App\Domain\ApiKeys\Entities\ApiKey;

interface ApiKeyWriteRepositoryInterface
{
    public function save(ApiKey $apiKey): void;
}
