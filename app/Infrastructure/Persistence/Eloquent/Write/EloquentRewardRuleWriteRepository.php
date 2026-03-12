<?php

namespace App\Infrastructure\Persistence\Eloquent\Write;

use App\Domain\Rewards\Contracts\Write\RewardRuleWriteRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Models\RewardRule as EloquentRewardRule;
use DateTimeImmutable;
use Exception;

class EloquentRewardRuleWriteRepository implements RewardRuleWriteRepositoryInterface
{
    public function save(RewardRule $rewardRule): RewardRule
    {
        $rule = EloquentRewardRule::create([
            'merchant_id' => $rewardRule->merchantId(),
            'event_type' => $rewardRule->eventType(),
            'name' => $rewardRule->name(),
            'reward_type' => $rewardRule->rewardType(),
            'reward_value' => $rewardRule->rewardValue(),
            'starts_at' => $rewardRule->startsAt(),
            'ends_at' => $rewardRule->endsAt(),
            'priority' => $rewardRule->priority(),
        ]);

        $rewardRule->setId($rule->id);

        return $rewardRule;
    }

    public function update(string $merchant_id, array $data): RewardRule
    {
        $rule = EloquentRewardRule::query()
            ->updateOrCreate([
                'merchant_id' => $merchant_id,
                'id' => $data['id'],
            ],
                $data);

        return $this->toDomain($rule);
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
