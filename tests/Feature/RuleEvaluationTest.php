<?php

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Entities\RewardRule;
use App\Exceptions\UnsupportedOperator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Rule Evaluation', function () {

    describe('Positives', function () {

        it('matches when the event type matches.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();
        });

        it('matches when payload condition satisfies.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => 'gte',
                        'value' => 1000,
                    ],
                ],
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();
        });

        it('matches when multiple conditions all satisfy.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'order_total' => 1500,
                    'customer_tier' => 'gold',
                ],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => 'gte',
                        'value' => 1000,
                    ],
                    [
                        'field' => 'customer_tier',
                        'operator' => 'eq',
                        'value' => 'gold',
                    ],
                ],
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();

        });

        it('matches when event occurred inside date range.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();
        });

        it('matches when rule has no payload conditions.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();

        });

        it('matches when rule has no date range.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeTrue();

        });

    });

    describe('Negatives', function () {

        it('does not match when event type differs.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.received',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();

        });

        it('does not match when rule inactive.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: false,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();

        });

        it('does not match when event occurred outside date range.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->addMonths(2)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        it('does not match when payload condition fails.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => 'gte',
                        'value' => 2000,
                    ],
                ]
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        it('does not match when payload condition field missing.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        it('does not match when condition value is not numeric.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(6)->format('Y-m-d H:i:s'),
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => 'gte',
                        'value' => '2000',
                    ],
                ],
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();
        });

        it('does not match when rule has expired.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->subMonths(2)->format('Y-m-d H:i:s'),
                ends_at: now()->subMonths(3)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();

        });

        it('does not match when rule not yet started.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: now()->addMonths(1)->format('Y-m-d H:i:s'),
                ends_at: now()->addMonths(2)->format('Y-m-d H:i:s'),
            );

            // When
            $matches = $rule->matches($event);

            // Then
            expect($matches)->toBeFalse();

        });

    });

    describe('Edge Cases', function () {

        it('throws when operator is unsupported.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => 'approximately',
                        'value' => 2000,
                    ],
                ]
            );

            // When
            $rule->matches($event);

            // Then
        })->throws(UnsupportedOperator::class);

    });

});
