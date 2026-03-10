<?php

namespace App\Domain\Rewards\Services;

use App\Domain\Rewards\Contracts\Read\RewardRuleReadRepositoryInterface;
use App\Domain\Rewards\Contracts\Write\RewardRuleWriteRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;

class RewardRuleService
{
    public function __construct(
        private readonly RewardRuleWriteRepositoryInterface $writeRepository,
        private readonly RewardRuleReadRepositoryInterface $readRepository
    ) {}

    public function save(RewardRule $rewardRule): RewardRule
    {
        return $this->writeRepository->save($rewardRule);
    }

    public function fetchAll(string $merchant_id): array
    {
        return $this->readRepository->fetchAll($merchant_id);
    }

    public function fetchById(int $id): RewardRule
    {
        return $this->readRepository->fetchById($id);
    }

    public function update(int $id, array $data): int
    {
        return $this->writeRepository->update($id, $data);
    }
}
