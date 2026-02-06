<?php

use App\Http\Domain\Events\Event;
use App\Http\Domain\Rewards\RewardInstruction;
use App\Http\Domain\Rules\Rule;
use App\Http\Domain\Rules\RuleEvaluator;

describe('Rule Evaluation', function () {

    describe('Payload & Process', function () {

        it('triggers a rule when the event matches.', function () {

            // Given
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

            // And
            $rule = Rule::whenEventType('order.completed')
                ->givePoints(10);

            // When
            $evaluator = new RuleEvaluator;
            $results = $evaluator->evaluate($event, [$rule]);

            // Then
            expect($results)->toHaveCount(1)
                ->and($results[0])->toBeInstanceOf(RewardInstruction::class)
                ->and(collect($results)->pluck('points'))->toContain(10);
        });

        it('triggers a rule when payload condition is satisfied.', function () {

            // Given
            $event = Event::fromPrimitives(
                id: 'EVT123',
                external_id: 'EXT123',
                type: 'order.completed',
                source: 'shopify',
                occurred_at: now()->toISOString(),
                payload: [
                    'order_total' => 1500,
                ]
            );

            // And
            $rule = Rule::whenEventType('order.completed')
                ->whenPayloadAtLeast('order_total', 1000)
                ->givePoints(10);

            // When
            $evaluator = new RuleEvaluator;
            $results = $evaluator->evaluate($event, [$rule]);

            // Then
            expect($results)->toHaveCount(1)
                ->and(collect($results)->pluck('points'))->toContain(10);
        });

        it('triggers multiple rules for a single matching event.', function () {

            // Given
            $event = Event::fromPrimitives(
                id: 'EVT123',
                external_id: 'EXT123',
                type: 'order.completed',
                source: 'shopify',
                occurred_at: now()->toISOString(),
                payload: [
                    'order_total' => 1500,
                ]
            );

            // And
            $ruleA = Rule::whenEventType('order.completed')->givePoints(10);
            $ruleB = Rule::whenEventType('order.completed')
                ->whenPayloadAtLeast('order_total', 1000)
                ->givePoints(5);

            // When
            $evaluator = new RuleEvaluator;
            $results = $evaluator->evaluate($event, [$ruleA, $ruleB]);

            expect($results)->toHaveCount(2)
                ->and(collect($results)->pluck('points'))->toContain(10, 5);
        });

        it('triggers multiple rewards when a rule defines multiple actions.', function () {

            // Given
            $event = Event::fromPrimitives(
                id: 'EVT123',
                external_id: 'EXT123',
                type: 'order.completed',
                source: 'shopify',
                occurred_at: now()->toISOString(),
                payload: [
                    'order_total' => 1500,
                ]
            );

            // And
            $rule = Rule::whenEventType('order.completed')
                ->whenPayloadAtLeast('order_total', 1000)
                ->givePoints(10)
                ->givePoints(5);

            // When
            $evaluator = new RuleEvaluator;
            $results = $evaluator->evaluate($event, [$rule]);

            expect($results)->toHaveCount(2);
            //                ->and($results[0]->getPoints())->toBe(10)
            //                ->and($results[1]->getPoints())->toBe(5);

        });
    });

    describe('Validation', function () {

        it('does not trigger a rule when the payload condition is satisfied.', function () {

            // Given
            $event = Event::fromPrimitives(
                id: 'EVT123',
                external_id: 'EXT123',
                type: 'order.completed',
                source: 'shopify',
                occurred_at: now()->toISOString(),
                payload: [
                    'order_total' => 500,
                ]
            );

            // And
            $rule = Rule::whenEventType('order.completed')
                ->whenPayloadAtLeast('order_total', 1000)
                ->givePoints(10);

            // When
            $evaluator = new RuleEvaluator;
            $results = $evaluator->evaluate($event, [$rule]);

            // Then
            expect($results)->toBeEmpty();
        });

        it('does not trigger a rule when the rule is disabled.', function () {

            // Given
            $event = Event::fromPrimitives(
                id: 'EVT123',
                external_id: 'EXT123',
                type: 'order.completed',
                source: 'shopify',
                occurred_at: now()->toISOString(),
                payload: [
                    'order_total' => 1500,
                ]
            );

            // And
            $rule = Rule::whenEventType('order.completed')
                ->givePoints(10)
                ->disable();

            // When
            $evaluator = new RuleEvaluator;
            $results = $evaluator->evaluate($event, [$rule]);

            // Then
            expect($results)->toBeEmpty();
        });
    });

});
