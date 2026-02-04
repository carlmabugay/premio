<?php

namespace App\Http\Domain\Events;

use App\Http\Application\Events\IngestEvent\IngestEventCommand;

readonly class Event
{
    public function __construct(
        public string $id,
        public string $external_id,
        public string $source,
        public string $type,
        public array $payload,
        public string $occurred_at,
    ) {}

    public static function fromCommand(IngestEventCommand $command): self
    {
        return new self(
            id: uuid_create(UUID_TYPE_RANDOM),
            external_id: $command->external_id,
            source: $command->source,
            type: $command->type,
            payload: $command->payload,
            occurred_at: $command->occurred_at,
        );
    }

    public static function fromPrimitives(
        string $id,
        string $external_id,
        string $type,
        string $source,
        string $occurred_at,
        array $payload
    ): self {
        return new self(
            id: $id,
            external_id: $external_id,
            source: $source,
            type: $type,
            payload: $payload,
            occurred_at: $occurred_at,
        );

    }
}
