<?php

namespace App\Application\UseCases;

use App\Domain\Rewards\Services\RewardRuleService;

readonly class HandleRewardRuleModification
{
    public function __construct(
        private RewardRuleService $service
    ) {}

    public function handle(int $id, array $data): int
    {
        return $this->service->update($id, $data);
    }
}
