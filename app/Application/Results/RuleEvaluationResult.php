<?php

namespace App\Application\Results;

readonly class RuleEvaluationResult
{
    public function __construct(
        public bool $already_evaluated,
        public int $matched_rules,
        public int $issued_rewards,
    ) {}
}
