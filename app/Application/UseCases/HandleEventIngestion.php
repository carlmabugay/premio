<?php

namespace App\Application\UseCases;

use App\Application\DTOs\CreateEventDTO;
use App\Application\Results\RuleEvaluationResult;
use App\Domain\Events\Entities\Event;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Str;

readonly class HandleEventIngestion
{
    public function __construct(
        private EvaluateRules $evaluateRules,
    ) {}

    /**
     * @throws Exception
     */
    public function handle(CreateEventDTO $dto): RuleEvaluationResult
    {
        $event = new Event(
            id: Str::uuid()->toString(),
            merchant_id: $dto->merchant_id,
            external_id: $dto->external_id,
            type: $dto->type,
            source: $dto->source,
            payload: $dto->payload,
            occurred_at: new DateTimeImmutable($dto->occurred_at),
            processed_at: null,
        );

        return $this->evaluateRules->execute($event);
    }
}
