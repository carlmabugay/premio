<?php

namespace App\Domain\Events\Entities;

use DateTimeImmutable;

readonly class Event
{
    public function __construct(
        private string $id,
        private string $merchant_id,
        private string $external_id,
        private string $type,
        private string $source,
        private ?array $payload,
        private DateTimeImmutable $occurred_at,
        private ?DateTimeImmutable $processed_at,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function merchantId(): string
    {
        return $this->merchant_id;
    }

    public function externalId(): string
    {
        return $this->external_id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function payload(): ?array
    {
        return $this->payload;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurred_at;
    }

    public function processedAt(): ?DateTimeImmutable
    {
        return $this->processed_at;
    }
}
