<?php

namespace App\Domain\Rewards\Services;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Exceptions\MalformedCondition;
use App\Exceptions\UnsupportedOperator;

class RewardEngine
{
    public function __construct(
        private readonly RewardRuleRepositoryInterface $ruleRepository,
        private ConditionEngine $conditionEngine,
    ) {}

    /**
     * @throws UnsupportedOperator | MalformedCondition
     */
    public function evaluate(Event $event): array
    {
        $rules = $this->ruleRepository->findActive($event->type());

        $matches = [];

        foreach ($rules as $rule) {
            if ($rule->matches($event, $this->conditionEngine)) {
                $matches[] = $rule;
            }

        }

        return $matches;
    }
}
