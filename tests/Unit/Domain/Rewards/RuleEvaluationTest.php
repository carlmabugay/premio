<?php

use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\ConditionEngine;
use App\Domain\Rewards\Services\RewardEngine;
use App\Exceptions\MalformedCondition;
use App\Exceptions\UnsupportedOperator;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->repository = Mockery::mock(RewardRuleRepositoryInterface::class);
    $this->conditionEngine = new ConditionEngine;
    $this->engine = new RewardEngine($this->repository, $this->conditionEngine);
});

describe('Unit: Rule Evaluation', function () {

    describe('Positives', function () {

        it('matches when the event type matches.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toHaveCount(1);
        });

        it('matches when payload condition satisfies.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => '>=',
                        'value' => 1000,
                    ],
                ],
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toHaveCount(1);
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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => '>=',
                        'value' => 1000,
                    ],
                    [
                        'field' => 'customer_tier',
                        'operator' => '=',
                        'value' => 'gold',
                    ],
                ],
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toHaveCount(1);

        });

        it('matches when event occurred inside date range.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: new DateTimeImmutable('2026-01-01 00:00:00'),
                ends_at: new DateTimeImmutable('2026-03-01 00:00:00'),
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toHaveCount(1);
        });

        it('matches when rule has no payload conditions.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toHaveCount(1);

        });

        it('matches when rule has no date range.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toHaveCount(1);

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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();

        });

        it('does not match when rule inactive.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: false,
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();

        });

        it('does not match when event occurred outside date range.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: new DateTimeImmutable('2026-02-01 00:00:00'),
                ends_at: new DateTimeImmutable('2026-03-01 00:00:00'),
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();
        });

        it('does not match when payload condition fails.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => '>',
                        'value' => 2000,
                    ],
                ]
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();
        });

        it('does not match when payload field missing.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();
        });

        it('does not match when condition value is not numeric.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => '>=',
                        'value' => '2000',
                    ],
                ],
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();
        });

        it('does not match when rule has expired.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: new DateTimeImmutable('2025-11-01 00:00:00'),
                ends_at: new DateTimeImmutable('2025-12-01 00:00:00'),
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();

        });

        it('does not match when rule not yet started.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                starts_at: new DateTimeImmutable('2026-05-01 00:00:00'),
                ends_at: new DateTimeImmutable('2026-07-01 00:00:00'),
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            // Then
            expect($matches)->toBeEmpty();

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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
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

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $this->engine->evaluate($event);

            // Then
        })->throws(UnsupportedOperator::class);

        it('does not match when condition JSON is malformed.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: ['order_total' => 1500],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            $malformedConditions = [
                [],
                [
                    ['field' => 'order_total', 'value' => 100],
                ],
                [
                    ['operator' => 'gte', 'value' => 100],
                ],
                [
                    ['field' => 'order_total', 'operator' => 'gte'],
                ],
            ];

            foreach ($malformedConditions as $conditions) {

                // And
                $rule = new RewardRule(
                    id: 1,
                    name: 'Active Rule',
                    event_type: 'order.completed',
                    reward_type: 'fixed',
                    reward_value: 100,
                    is_active: true,
                    conditions: $conditions,
                );

                // When
                $this->repository->shouldReceive('findActive')
                    ->once()
                    ->andReturn([$rule]);

                // When
                $this->engine->evaluate($event);
            }
            // Then
        })->throws(MalformedCondition::class);

        it('ignores unknown payload fields safely.', function () {
            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'order_total' => 1500,
                    'notes' => 'VIP order',
                ],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => '>=',
                        'value' => 1000,
                    ],
                ],
            );

            // When
            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            expect($matches)->toHaveCount(1);
        });

        it('matches correctly when payload value is string and rule expects string.', function () {
            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'customer_tier' => 'gold',
                ],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'customer_tier',
                        'operator' => '=',
                        'value' => 'gold',
                    ],
                ],
            );

            // When
            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            expect($matches)->toHaveCount(1);
        });

        it('matches correctly when payload value is numeric string and rule expects numeric.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'order_total' => 1500,
                    'order_quantity' => 2,
                ],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_quantity',
                        'operator' => '>=',
                        'value' => 2,
                    ],
                ],
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            expect($matches)->toHaveCount(1);
        });

        it('handles null payload values safely.', function () {
            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: null,
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_quantity',
                        'operator' => 'gte',
                        'value' => 2,
                    ],
                ],
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            expect($matches)->toBeEmpty();
        });

        it('handles boolean comparisons correctly.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'is_authenticated' => true,
                ],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $rule = new RewardRule(
                id: 1,
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'is_authenticated',
                        'operator' => '=',
                        'value' => true,
                    ],
                ],
            );

            $this->repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$rule]);

            // When
            $matches = $this->engine->evaluate($event);

            expect($matches)->toHaveCount(1);

        });

    });

});
