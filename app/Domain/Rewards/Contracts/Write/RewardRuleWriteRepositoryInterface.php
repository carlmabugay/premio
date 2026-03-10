<?php

namespace App\Domain\Rewards\Contracts\Write;

use App\Domain\Rewards\Entities\RewardRule;

interface RewardRuleWriteRepositoryInterface
{
    public function save(RewardRule $rewardRule): RewardRule;

    public function update(int $id, array $data): int;
}
