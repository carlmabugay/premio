<?php

namespace App\Http\Application\Events\IngestEvent;

readonly class IngestEventCommand
{
    public function __construct(
        public string $external_id,
        public string $source,
        public string $type,
        public array $payload,
        public string $occurred_at,
    ) {}
}
