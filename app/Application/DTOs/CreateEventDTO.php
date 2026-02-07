<?php

namespace App\Application\DTOs;

class CreateEventDTO
{
    public function __construct(
        public string $external_id,
        public string $type,
        public string $source,
        public array $payload,
        public string $occurred_at,
    ) {}

    public static function fromArray(array $event): self
    {
        return new self(
            external_id: $event['external_id'],
            type: $event['type'],
            source: $event['source'],
            payload: $event['payload'],
            occurred_at: $event['occurred_at'],
        );
    }
}
