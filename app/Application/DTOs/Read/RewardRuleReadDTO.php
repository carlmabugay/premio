<?php

namespace App\Application\DTOs\Read;

use App\Domain\Rewards\Entities\RewardRule;
use DateTimeImmutable;

readonly class RewardRuleReadDTO
{
    public function __construct(
        public int $id,
        public string $merchant_id,
        public string $name,
        public string $event_type,
        public string $reward_type,
        public float $reward_value,
        public bool $is_active,
        public DateTimeImmutable $starts_at,
        public DateTimeImmutable $ends_at,
        public ?float $priority,
    ) {}

    public static function fromEntity(RewardRule $rule): self
    {
        return new self(
            id: $rule->id(),
            merchant_id: $rule->merchantId(),
            name: $rule->name(),
            event_type: $rule->eventType(),
            reward_type: $rule->rewardType(),
            reward_value: $rule->rewardValue(),
            is_active: $rule->isActive(),
            starts_at: $rule->startsAt(),
            ends_at: $rule->endsAt(),
            priority: $rule->priority(),
        );
    }

    public static function fromEntityCollection(array $rules): array
    {
        return collect($rules)
            ->map(fn ($rule) => self::fromEntity($rule))
            ->toArray();
    }
}
