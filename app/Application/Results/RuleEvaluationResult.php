<?php

namespace App\Application\Results;

readonly class RuleEvaluationResult
{
    public function __construct(
        public bool $already_evaluated,
        public int $issued_rewards,
        public array $issued_issues,
        public string $event_id,
    ) {}

    public static function processed(string $event_id, array $issues): self
    {
        return new self(
            already_evaluated: false,
            issued_rewards: count($issues),
            issued_issues: $issues,
            event_id: $event_id,
        );
    }

    public static function alreadyProcessed(string $event_id): self
    {
        return new self(
            already_evaluated: true,
            issued_rewards: 0,
            issued_issues: [],
            event_id: $event_id,
        );
    }
}
