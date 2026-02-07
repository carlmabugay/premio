<?php

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Conditions\GreaterThanOrEqualCondition;
use App\Domain\Rewards\Entities\RewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Rule Evaluation', function () {

    describe('Payload & Process', function () {

        it('matches when the event type matches.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();
        });

        it('matches when payload condition satisfies.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            $condition = new GreaterThanOrEqualCondition(
                field: 'order_total',
                value: 1000,
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
                condition: $condition,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();
        });

        it('matches when event occurred inside date range.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            $condition = new GreaterThanOrEqualCondition(
                field: 'order_total',
                value: 1000,
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
                condition: $condition,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();
        });

        it('does not match when event type differs.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.received',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();

        });

        it('does not match when rule inactive.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: false,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();

        });

        it('does not match when event occurred outside date range.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            $condition = new GreaterThanOrEqualCondition(
                field: 'order_total',
                value: 1000,
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->addMonths(2)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
                condition: $condition,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        it('does not match when payload condition fails.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            $condition = new GreaterThanOrEqualCondition(
                field: 'order_total',
                value: 2000,
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
                condition: $condition,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        it('does not match when payload condition field missing.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        it('does not match when condition value is not numeric.', function () {

            // Given
            $event = Event::record(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            $condition = new GreaterThanOrEqualCondition(
                field: 'order_total',
                value: '2000',
            );

            // And
            $rule = RewardRule::create(
                id: '1',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                is_active: true,
                condition: $condition,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        //        it('triggers multiple rules for a single matching event.', function () {
        //
        //            // Given
        //            $event = Event::fromPrimitives(
        //                id: 'EVT123',
        //                external_id: 'EXT123',
        //                type: 'order.completed',
        //                source: 'shopify',
        //                occurred_at: now()->toISOString(),
        //                payload: [
        //                    'order_total' => 1500,
        //                ]
        //            );
        //
        //            // And
        //            $ruleA = Rule::whenEventType('order.completed')->givePoints(10);
        //            $ruleB = Rule::whenEventType('order.completed')
        //                ->whenPayloadAtLeast('order_total', 1000)
        //                ->givePoints(5);
        //
        //            // When
        //            $evaluator = new RuleEvaluator;
        //            $results = $evaluator->evaluate($event, [$ruleA, $ruleB]);
        //
        //            expect($results)->toHaveCount(2)
        //                ->and(collect($results)->pluck('points'))->toContain(10, 5);
        //        });

        //        it('triggers multiple rewards when a rule defines multiple actions.', function () {
        //
        //            // Given
        //            $event = Event::fromPrimitives(
        //                id: 'EVT123',
        //                external_id: 'EXT123',
        //                type: 'order.completed',
        //                source: 'shopify',
        //                occurred_at: now()->toISOString(),
        //                payload: [
        //                    'order_total' => 1500,
        //                ]
        //            );
        //
        //            // And
        //            $rule = Rule::whenEventType('order.completed')
        //                ->whenPayloadAtLeast('order_total', 1000)
        //                ->givePoints(10)
        //                ->givePoints(5);
        //
        //            // When
        //            $evaluator = new RuleEvaluator;
        //            $results = $evaluator->evaluate($event, [$rule]);
        //
        //            expect($results)->toHaveCount(2);
        //            //                ->and($results[0]->getPoints())->toBe(10)
        //            //                ->and($results[1]->getPoints())->toBe(5);
        //
        //        });
    });

    describe('Validation', function () {

        //        it('does not trigger a rule when the payload condition is satisfied.', function () {
        //
        //            // Given
        //            $event = Event::fromPrimitives(
        //                id: 'EVT123',
        //                external_id: 'EXT123',
        //                type: 'order.completed',
        //                source: 'shopify',
        //                occurred_at: now()->toISOString(),
        //                payload: [
        //                    'order_total' => 500,
        //                ]
        //            );
        //
        //            // And
        //            $rule = Rule::whenEventType('order.completed')
        //                ->whenPayloadAtLeast('order_total', 1000)
        //                ->givePoints(10);
        //
        //            // When
        //            $evaluator = new RuleEvaluator;
        //            $results = $evaluator->evaluate($event, [$rule]);
        //
        //            // Then
        //            expect($results)->toBeEmpty();
        //        });

        //        it('does not trigger a rule when the rule is disabled.', function () {
        //
        //            // Given
        //            $event = Event::fromPrimitives(
        //                id: 'EVT123',
        //                external_id: 'EXT123',
        //                type: 'order.completed',
        //                source: 'shopify',
        //                occurred_at: now()->toISOString(),
        //                payload: [
        //                    'order_total' => 1500,
        //                ]
        //            );
        //
        //            // And
        //            $rule = Rule::whenEventType('order.completed')
        //                ->givePoints(10)
        //                ->disable();
        //
        //            // When
        //            $evaluator = new RuleEvaluator;
        //            $results = $evaluator->evaluate($event, [$rule]);
        //
        //            // Then
        //            expect($results)->toBeEmpty();
        //        });
    });

});
