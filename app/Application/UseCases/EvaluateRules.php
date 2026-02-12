<?php

namespace App\Application\UseCases;

use App\Application\Results\RuleEvaluationResult;
use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;

readonly class EvaluateRules
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private RewardRuleRepositoryInterface $ruleRepository,
    ) {}

    public function execute(Event $event): RuleEvaluationResult
    {
        if ($this->eventRepository->exists($event)) {
            return new RuleEvaluationResult(
                already_evaluated: true,
                matched_rules: 0,
                issued_rewards: 0,
            );
        }

        $this->eventRepository->save($event);

        $active_rules = $this->ruleRepository->findActive();

        usort($active_rules, fn ($a, $b) => $a->priority <=> $b->priority);

        $matched = 0;

        foreach ($active_rules as $rule) {
            if ($rule->matches($event)) {
                $matched++;
            }
        }

        return new RuleEvaluationResult(
            already_evaluated: false,
            matched_rules: $matched,
            issued_rewards: $matched,
        );
    }
}
