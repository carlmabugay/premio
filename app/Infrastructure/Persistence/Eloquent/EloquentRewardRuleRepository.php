<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Models\RewardRule as EloquentRewardRule;
use DateTimeImmutable;
use Exception;

class EloquentRewardRuleRepository implements RewardRuleRepositoryInterface
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

    /**
     * @throws Exception
     */
    private function toDomain(EloquentRewardRule $model): RewardRule
    {
        return new RewardRule(
            id: $model->id,
            name: $model->name,
            event_type: $model->event_type,
            reward_type: $model->reward_type,
            reward_value: $model->reward_value,
            is_active: $model->is_active,
            starts_at: $model->starts_at ? new DateTimeImmutable($model->starts_at) : null,
            ends_at: $model->ends_at ? new DateTimeImmutable($model->ends_at) : null,
            conditions: json_decode($model->conditions),
            priority: $model->priority,
        );
    }
}
