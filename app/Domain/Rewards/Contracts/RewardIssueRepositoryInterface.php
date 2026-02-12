<?php

namespace App\Domain\Rewards\Contracts;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Entities\RewardRule;

interface RewardIssueRepositoryInterface
{
    public function issue(Event $event, RewardRule $rule): void;
}
