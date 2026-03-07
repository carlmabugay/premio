<?php

namespace App\Infrastructure\Persistence\Eloquent\Write;

use App\Domain\Rewards\Contracts\Write\RewardRuleWriteRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Models\RewardRule as EloquentRewardRule;

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
}
