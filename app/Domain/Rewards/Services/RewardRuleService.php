<?php

namespace App\Domain\Rewards\Services;

use App\Domain\Rewards\Contracts\Read\RewardRuleReadRepositoryInterface;
use App\Domain\Rewards\Contracts\Write\RewardRuleWriteRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;

readonly class RewardRuleService
{
    public function __construct(
        private RewardRuleWriteRepositoryInterface $writeRepository,
        private RewardRuleReadRepositoryInterface $readRepository
    ) {}

    public function save(RewardRule $rewardRule): RewardRule
    {
        return $this->writeRepository->save($rewardRule);
    }

    public function fetchAll(): array
    {
        return $this->readRepository->fetchAll();
    }

    public function fetchById(int $id): RewardRule
    {
        return $this->readRepository->fetchById($id);
    }
}
