<?php

namespace App\Domain\Rewards\Contracts\Write;

use App\Domain\Rewards\Entities\RewardRule;

interface RewardRuleWriteRepositoryInterface
{
    public function save(RewardRule $rewardRule): RewardRule;

    public function update(string $merchant_id, int $id, array $data): int;
}
