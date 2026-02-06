<?php

namespace App\Http\Application\EventIngestion;

use App\Http\Domain\Events\Event;
use App\Http\Domain\Events\EventRepository;
use App\Http\Domain\Rewards\RewardRepository;
use App\Http\Domain\Rules\Rule;
use App\Http\Domain\Rules\RuleEvaluator;
use Illuminate\Support\Str;

final readonly class HandleIncomingEvent
{
    public function __construct(
        private EventRepository $eventRepository,
        private RuleEvaluator $rules,
        private RewardRepository $rewardRepository,
    ) {}

    public function handle(IncomingEventDTO $dto): IngestionResult
    {
        if ($this->eventRepository->existsByExternalId($dto->external_id, $dto->source)) {
            return IngestionResult::duplicate();
        }

        $event = Event::fromPrimitives(
            id: Str::uuid()->toString(),
            external_id: $dto->external_id,
            type: $dto->type,
            source: $dto->source,
            occurred_at: $dto->occurred_at,
            payload: $dto->payload,
        );

        $this->eventRepository->save($event);

        // TODO: Must be from RewardRule Model.
        $rule = Rule::whenEventType($dto->type)
            ->whenPayloadAtLeast('customer_id', 1)
            ->givePoints(10);

        $rewards = $this->rules->evaluate($event, [$rule]);

        foreach ($rewards as $reward) {
            $this->rewardRepository->issue($reward);
        }

        return IngestionResult::created();
    }
}
