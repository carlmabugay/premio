<?php

namespace App\Http\Application\EventIngestion;

class IngestionResult
{
    private function __construct(
        public string $status,
        private readonly int $http_status
    ) {}

    public static function created(): self
    {
        return new self('created', 201);
    }

    public static function duplicate(): self
    {
        return new self('duplicate', 200);
    }

    public function httpStatus(): int
    {
        return $this->http_status;
    }
}
