<?php

namespace App\Application\Results;

readonly class IngestionResult
{
    public function __construct(
        public string $event_id,
        public int $status_code,
        public bool $is_duplicate,
    ) {}

    public static function created(string $event_id): self
    {
        return new self(
            event_id: $event_id,
            status_code: 201,
            is_duplicate: false,
        );
    }

    public static function duplicate(string $event_id): self
    {
        return new self(
            event_id: $event_id,
            status_code: 200,
            is_duplicate: true,
        );
    }
}
