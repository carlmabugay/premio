<?php

namespace App\Application\UseCases;

use App\Application\Results\RuleEvaluationResult;
use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Services\RewardEngine;

readonly class EvaluateRules
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private RewardRuleRepositoryInterface $ruleRepository,
        private RewardIssueRepositoryInterface $issueRepository,
        private RewardEngine $rewardEngine,
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

        $matches = $this->rewardEngine->evaluate($event);
        usort($matches, fn ($a, $b) => $a->priority <=> $b->priority);

        $issued = 0;

        foreach ($matches as $rule) {
            $this->issueRepository->issue($event, $rule);
            $issued++;
        }

        return new RuleEvaluationResult(
            already_evaluated: false,
            matched_rules: $issued,
            issued_rewards: $issued,
        );
    }
}
