<?php

namespace App\Domain\Rewards\Entities;

use App\Domain\Events\Entities\Event;
use App\Exceptions\MalformedCondition;
use App\Exceptions\UnsupportedOperator;

readonly class RewardRule
{
    public function __construct(
        private int $id,
        private string $event_type,
        private string $reward_type,
        private int $reward_value,
        private bool $is_active,
        private ?string $starts_at = null,
        private ?string $ends_at = null,
        private ?array $conditions = []
    ) {}

    /**
     * @throws UnsupportedOperator
     */
    public function matches(Event $event): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->event_type !== $event->type()) {
            return false;
        }

        if (empty($event->payload())) {
            return false;
        }

        if (! $this->isWithinDateRange($event)) {
            return false;
        }

        return $this->evaluateConditions($event->payload());

    }

    private function isWithinDateRange(Event $event): bool
    {
        $occurred_at = $event->occurred_at();

        if (! $this->starts_at || ! $this->ends_at) {
            return true;
        }

        if ($occurred_at < $this->starts_at) {
            return false;
        }

        if ($occurred_at > $this->ends_at) {
            return false;
        }

        return true;
    }

    /**
     * @throws UnsupportedOperator
     * @throws MalformedCondition
     */
    private function evaluateConditions(array $payload): bool
    {
        foreach ($this->conditions as $condition) {

            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? null;
            $value = $condition['value'] ?? null;

            if (! $field || ! $operator || ! array_key_exists('value', $condition)) {
                throw new MalformedCondition(json_encode($condition));
            }

            if (! array_key_exists($field, $payload)) {
                return false;
            }

            $actual = $payload[$field];

            if (! $this->evaluateOperator($actual, $operator, $value)) {
                return false;
            }

        }

        return true;
    }

    /**
     * @throws UnsupportedOperator
     */
    private function evaluateOperator($actual, string $operator, $expected): bool
    {
        return match ($operator) {
            'eq' => $actual === $expected,
            'gt' => is_numeric($actual) && $actual > $expected,
            'gte' => is_numeric($actual) && $actual >= $expected,
            'lt' => is_numeric($actual) && $actual < $expected,
            'lte' => is_numeric($actual) && $actual <= $expected,
            default => throw new UnsupportedOperator($operator)
        };
    }
}
