<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\ApiKeys\Contracts\ApiKeyRepositoryInterface;
use App\Domain\ApiKeys\Entities\ApiKey;
use App\Models\ApiKey as EloquentApiKey;

class EloquentApiKeyRepository implements ApiKeyRepositoryInterface
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

    public function exists(string $key): bool
    {
        return EloquentApiKey::where('key_hash', $key)
            ->where('is_active', true)
            ->exists();
    }
}
