<?php

namespace App\Application\UseCases;

use App\Application\Results\RuleEvaluationResult;
use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Entities\RewardIssue;
use App\Domain\Rewards\Services\RewardEngine;
use App\Exceptions\MalformedCondition;
use App\Exceptions\UnsupportedOperator;

readonly class EvaluateRules
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private RewardIssueRepositoryInterface $issueRepository,
        private RewardEngine $rewardEngine,
    ) {}

    /**
     * @throws MalformedCondition | UnsupportedOperator
     */
    public function execute(Event $event): RuleEvaluationResult
    {
        $issues = [];

        if ($this->eventRepository->exists($event)) {
            return RuleEvaluationResult::alreadyProcessed($event->id());
        }

        $this->eventRepository->save($event);

        $matches = $this->rewardEngine->evaluate($event);
        usort($matches, fn ($a, $b) => $a->priority <=> $b->priority);

        foreach ($matches as $rule) {

            $issue = new RewardIssue(
                event_id: $event->id(),
                reward_rule_id: $rule->id(),
                reward_type: $rule->rewardType(),
                reward_value: $rule->rewardValue(),
            );

            $this->issueRepository->issue($issue);
            $issues[] = $issue;
        }

        return RuleEvaluationResult::processed($event->id(), $issues);
    }
}
