<?php

namespace App\Http\Application\EventIngestion;

final class IncomingEventDTO
{
    public function __construct(
        public string $external_id,
        public string $source,
        public string $type,
        public array $payload,
        public string $occurred_at,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            external_id: $request['external_id'],
            source: $request['source'],
            type: $request['type'],
            payload: $request['payload'],
            occurred_at: $request['occurred_at'],
        );
    }
}
