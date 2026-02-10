<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Models\RewardRule as EloquentRewardRule;

class EloquentRewardRuleRepository implements RewardRuleRepositoryInterface
{
    public function findActive(): array
    {
        return EloquentRewardRule::where('active', true)
            ->get()
            ->map(fn ($model) => new RewardRule(
                id: $model->id,
                event_type: $model->event_type,
                reward_type: $model->reward_type,
                reward_value: $model->reward_value,
                is_active: $model->active,
                starts_at: $model->starts_at,
                ends_at: $model->ends_at,
                conditions: $model->condition ?? [],
            ))
            ->toArray();
    }
}
