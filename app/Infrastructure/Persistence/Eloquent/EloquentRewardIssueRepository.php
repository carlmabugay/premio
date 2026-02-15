<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Models\RewardIssue as EloquentRewardIssue;

class EloquentRewardIssueRepository implements RewardIssueRepositoryInterface
{
    public function issue(Event $event, RewardRule $rule): void
    {
        EloquentRewardIssue::create([
            'event_id' => $event->id(),
            'reward_rule_id' => $rule->id(),
            'reward_type' => $rule->rewardType(),
            'reward_value' => $rule->rewardValue(),
        ]);
    }
}
