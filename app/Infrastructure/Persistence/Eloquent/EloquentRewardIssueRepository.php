<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Entities\RewardIssue;
use App\Models\RewardIssue as EloquentRewardIssue;

class EloquentRewardIssueRepository implements RewardIssueRepositoryInterface
{
    public function issue(RewardIssue $rewardIssue): void
    {
        EloquentRewardIssue::create([
            'event_id' => $rewardIssue->eventId(),
            'reward_rule_id' => $rewardIssue->rewardRuleId(),
            'reward_type' => $rewardIssue->rewardType(),
            'reward_value' => $rewardIssue->rewardValue(),
        ]);
    }
}
