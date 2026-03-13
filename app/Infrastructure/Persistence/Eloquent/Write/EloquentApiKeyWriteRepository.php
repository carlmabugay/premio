<?php

namespace App\Infrastructure\Persistence\Eloquent\Write;

use App\Domain\ApiKeys\Contracts\Write\ApiKeyWriteRepositoryInterface;
use App\Domain\ApiKeys\Entities\ApiKey;
use App\Models\ApiKey as EloquentApiKey;

class EloquentApiKeyWriteRepository implements ApiKeyWriteRepositoryInterface
{
    public function save(ApiKey $apiKey): void
    {
        EloquentApiKey::create([
            'merchant_id' => $apiKey->merchantId(),
            'name' => $apiKey->name(),
            'key_hash' => $apiKey->keyHash(),
            'is_active' => $apiKey->isActive(),
        ]);
    }
}
