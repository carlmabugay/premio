<?php

namespace App\Domain\Rewards\Services;

class ConditionEngine
{
    public function matches(array $conditions, array $payload): bool
    {
        foreach ($conditions as $condition) {

            $field = $condition['field'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            if (! array_key_exists($field, $payload)) {
                continue;
            }

            $actual = $payload[$field];

            if (! $this->compare($actual, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    private function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            '=' => $actual === $expected,
            '!=' => $actual !== $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            default => false,
        };
    }
}
