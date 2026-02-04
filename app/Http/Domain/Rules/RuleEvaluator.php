<?php

namespace App\Http\Domain\Rules;

use App\Http\Domain\Events\Event;

final class RuleEvaluator
{
    public function evaluate(Event $event, array $rules): array
    {
        $results = [];

        foreach ($rules as $rule) {
            if (! $rule->matches($event)) {
                continue;
            }

            $results[] = $rule->toRewardInstruction();
        }

        return $results;
    }
}
