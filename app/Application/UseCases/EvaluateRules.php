<?php

namespace App\Application\UseCases;

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;

readonly class EvaluateRules
{
    public function __construct(
        private RewardRuleRepositoryInterface $rules
    ) {}

    public function execute(Event $event): array
    {
        $active_rules = $this->rules->findActive();

        usort($active_rules, fn ($a, $b) => $a->priority <=> $b->priority);

        $matching = [];

        foreach ($active_rules as $rule) {
            if ($rule->matches($event)) {
                $matching[] = $rule;
            }
        }

        return $matching;
    }
}
