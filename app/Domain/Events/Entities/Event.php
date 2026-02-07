<?php

namespace App\Domain\Events\Entities;

readonly class Event
{
    public function __construct(
        private string $id,
        private string $external_id,
        private string $type,
        private string $source,
        private array $payload,
        private string $occurred_at,
    ) {}

    public static function record(string $id, string $external_id, string $type, string $source, array $payload, string $occurred_at): self
    {
        return new self(
            id: $id,
            external_id: $external_id,
            type: $type,
            source: $source,
            payload: $payload,
            occurred_at: $occurred_at
        );
    }

    public function id(): string
    {
        return $this->id;
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

    public function payload(): array
    {
        return $this->payload;
    }

    public function occurred_at(): string
    {
        return $this->occurred_at;
    }
}
