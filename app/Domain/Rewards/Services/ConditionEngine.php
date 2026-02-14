<?php

namespace App\Domain\Rewards\Services;

use App\Exceptions\MalformedCondition;
use App\Exceptions\UnsupportedOperator;

class ConditionEngine
{
    /**
     * @throws MalformedCondition|UnsupportedOperator
     */
    public function matches(array $conditions, array $payload): bool
    {
        foreach ($conditions as $condition) {

            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? null;
            $value = $condition['value'] ?? null;

            if (! $field || ! $operator || ! array_key_exists($field, $payload)) {
                throw new MalformedCondition(json_encode($condition));
            }

            $actual = $payload[$field];

            if (! $this->compare($actual, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws UnsupportedOperator
     */
    private function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            '=' => $actual === $expected,
            '!=' => $actual !== $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            default => throw new UnsupportedOperator($operator),
        };
    }
}
