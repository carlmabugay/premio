<?php

namespace App\Infrastructure\Persistence\Eloquent\Read;

use App\Domain\ApiKeys\Contracts\Read\ApiKeyReadRepositoryInterface;
use App\Domain\ApiKeys\Entities\ApiKey;
use App\Models\ApiKey as EloquentApiKey;

class EloquentApiKeyReadRepository implements ApiKeyReadRepositoryInterface
{
    public function exists(string $key): bool
    {
        return EloquentApiKey::where('key_hash', $key)
            ->where('is_active', true)
            ->exists();
    }

    public function fetchByApiKey(string $key): ApiKey
    {
        $key = EloquentApiKey::query()->where('key_hash', $key)->first();

        return $this->toDomain($key);
    }

    private function toDomain(EloquentApiKey $key): ApiKey
    {
        return new ApiKey(
            merchant_id: $key->merchant_id,
            name: $key->name,
            key_hash: $key->key_hash,
            is_active: $key->is_active,
        );
    }
}
