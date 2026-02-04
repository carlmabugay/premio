<?php

use App\Http\Domain\Events\Event;
use App\Http\Domain\Rewards\RewardInstruction;
use App\Http\Domain\Rules\Rule;
use App\Http\Domain\Rules\RuleEvaluator;

describe('Rule Evaluation', function () {

    describe('Payload & Process', function () {

        it('triggers a rule when the event matches', function () {

            $event = Event::fromPrimitives(
                id: 'EVT123',
                external_id: 'EXT123',
                type: 'order.completed',
                source: 'shopify',
                occurred_at: now()->toISOString(),
                payload: [
                    'order_total' => 1500,
                ],
            );

            $rule = Rule::whenEventType('order.completed')
                ->givePoints(10);

            $evaluator = new RuleEvaluator;

            $results = $evaluator->evaluate($event, [$rule]);

            expect($results)->toHaveCount(1)
                ->and($results[0])->toBeInstanceOf(RewardInstruction::class)
                ->and($results[0]->points)->toBe(10);
        });
    });

    describe('Validation', function () {});

});
