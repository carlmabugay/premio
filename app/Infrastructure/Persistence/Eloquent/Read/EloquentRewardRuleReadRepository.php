<?php

namespace App\Infrastructure\Persistence\Eloquent\Read;

use App\Domain\Rewards\Contracts\Read\RewardRuleReadRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Models\RewardRule as EloquentRewardRule;
use DateTimeImmutable;
use Exception;

class EloquentRewardRuleReadRepository implements RewardRuleReadRepositoryInterface
{
    public function findActive(string $event_type): array
    {
        return EloquentRewardRule::query()
            ->where('is_active', true)
            ->where('event_type', $event_type)
            ->orderBy('priority')
            ->get()
            ->map(fn ($model) => $this->toDomain($model))
            ->all();
    }

    public function fetchAll(string $merchant_id): array
    {
        return EloquentRewardRule::query()
            ->where('merchant_id', $merchant_id)
            ->get()
            ->map(fn ($model) => $this->toDomain($model))
            ->all();
    }

    public function fetchById(string $merchant_id, int $id): RewardRule
    {
        $model = EloquentRewardRule::query()
            ->where('id', $id)
            ->where('merchant_id', $merchant_id)
            ->first();

        return $this->toDomain($model);
    }

    /**
     * @throws Exception
     */
    private function toDomain(EloquentRewardRule $model): RewardRule
    {
        return new RewardRule(
            merchant_id: $model->merchant_id,
            name: $model->name,
            event_type: $model->event_type,
            reward_type: $model->reward_type,
            reward_value: $model->reward_value,
            is_active: $model->is_active,
            starts_at: $model->starts_at ? new DateTimeImmutable($model->starts_at) : null,
            ends_at: $model->ends_at ? new DateTimeImmutable($model->ends_at) : null,
            conditions: json_decode($model->conditions),
            priority: $model->priority,
            id: $model->id,
        );
    }
}
