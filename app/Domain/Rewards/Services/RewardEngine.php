<?php

namespace App\Domain\Rewards\Services;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;

readonly class RewardEngine
{
    public function __construct(
        private RewardRuleRepositoryInterface $ruleRepository,
        private ConditionEngine $conditionEngine,
    ) {}

    public function evaluate(Event $event): array
    {
        $rules = $this->ruleRepository->findActive();

        $matches = [];

        foreach ($rules as $rule) {

            if (! $rule->isActive()) {
                continue;
            }

            if ($rule->eventType() !== $event->type()) {
                continue;
            }

            if (! $rule->isWithinWindow($event->occurredAt())) {
                continue;
            }

            if ($this->conditionEngine->matches($rule->conditions(), $event->payload())) {
                $matches[] = $rule;
            }
        }

        return $matches;
    }
}
