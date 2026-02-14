<?php

namespace App\Domain\Rewards\Services;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Exceptions\MalformedCondition;
use App\Exceptions\UnsupportedOperator;

readonly class RewardEngine
{
    public function __construct(
        private RewardRuleRepositoryInterface $ruleRepository,
        private ConditionEngine $conditionEngine,
    ) {}

    /**
     * @throws UnsupportedOperator
     * @throws MalformedCondition
     */
    public function evaluate(Event $event): array
    {
        $rules = $this->ruleRepository->findActive($event->type());

        $matches = [];

        foreach ($rules as $rule) {

            if (! $rule->isActive()) {
                continue;
            }

            if ($rule->eventType() !== $event->type()) {
                continue;
            }

            if (empty($event->payload())) {
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
