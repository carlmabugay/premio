<?php

namespace App\Application\DTOs;

use DateTimeImmutable;
use Exception;

class CreateRewardRuleDTO
{
    public function __construct(
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

    /**
     * @throws Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            merchant_id: $data['merchant_id'],
            name: $data['name'],
            event_type: $data['event_type'],
            reward_type: $data['reward_type'],
            reward_value: $data['reward_value'],
            is_active: $data['is_active'],
            starts_at: new DateTimeImmutable($data['starts_at']),
            ends_at: new DateTimeImmutable($data['ends_at']),
            priority: $data['priority'],
        );
    }
}
