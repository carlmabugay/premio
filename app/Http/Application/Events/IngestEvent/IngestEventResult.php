<?php

namespace App\Http\Application\Events\IngestEvent;

final readonly class IngestEventResult
{
    public function __construct(
        public bool $was_created,
    ) {}

    public static function created(): self
    {
        return new self(true);
    }

    public static function duplicate(): self
    {
        return new self(false);
    }
}
