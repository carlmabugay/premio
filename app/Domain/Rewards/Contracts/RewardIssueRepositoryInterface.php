<?php

namespace App\Domain\Rewards\Contracts;

use App\Domain\Rewards\Entities\RewardIssue;

interface RewardIssueRepositoryInterface
{
    public function issue(RewardIssue $rewardIssue): void;
}
