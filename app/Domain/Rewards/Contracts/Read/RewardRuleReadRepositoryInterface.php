<?php

namespace App\Domain\Rewards\Contracts\Read;

interface RewardRuleReadRepositoryInterface
{
    public function findActive(string $event_type): array;

    public function fetchAll(): array;
}
