<?php

namespace App\Domain\Rewards\Contracts\Read;

use App\Domain\Rewards\Entities\RewardRule;

interface RewardRuleReadRepositoryInterface
{
    public function findActive(string $event_type): array;

    public function fetchAll(string $merchant_id): array;

    public function fetchById(string $merchant_id, int $id): RewardRule;
}
