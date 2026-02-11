<?php

use App\Application\UseCases\EvaluateRules;
use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;

describe('Reward Evaluation Feature', function () {

    describe('Positives', function () {

        it('evaluates multiple rules and returns only matching rules.', function () {

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
            $matchingRule = new RewardRule(
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
                ]
            );

            $nonMatchingRule = new RewardRule(
                id: 2,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => 'gte',
                        'value' => 2000,
                    ],
                ]
            );

            // When
            $repository = Mockery::mock(RewardRuleRepositoryInterface::class);
            $repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$matchingRule, $nonMatchingRule]);

            $useCase = new EvaluateRules($repository);

            $result = $useCase->execute($event);

            // Then
            expect($result)->toHaveCount(1)
                ->and($result[0]->id())->toBe(1);

        });

        it('evaluates multiple rules where some match and some don’t.', function () {
            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'amount' => 150,
                    'currency' => 'USD',
                ],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $matchingRule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'amount',
                        'operator' => 'gte',
                        'value' => 100,
                    ],
                ]
            );

            // And
            $nonMatchingRule = new RewardRule(
                id: 2,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'amount',
                        'operator' => 'gte',
                        'value' => 500,
                    ],
                ],
            );

            // When
            $repository = Mockery::mock(RewardRuleRepositoryInterface::class);
            $repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$matchingRule, $nonMatchingRule]);

            $useCase = new EvaluateRules($repository);

            $result = $useCase->execute($event);

            // Then
            expect($result)->toHaveCount(1)
                ->and($result[0]->id())->toBe(1);

        });

        it('returns empty result when no rules match.', function () {
            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'amount' => 150,
                    'currency' => 'USD',
                ],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            // And
            $nonMatchingRuleOne = new RewardRule(
                id: 2,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'amount',
                        'operator' => 'gte',
                        'value' => 500,
                    ],
                ],
            );

            // And
            $nonMatchingRuleTwo = new RewardRule(
                id: 2,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'amount',
                        'operator' => 'lte',
                        'value' => 50,
                    ],
                ],
            );

            // When
            $repository = Mockery::mock(RewardRuleRepositoryInterface::class);
            $repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$nonMatchingRuleOne, $nonMatchingRuleTwo]);

            $useCase = new EvaluateRules($repository);

            $result = $useCase->execute($event);

            expect($result)->toBeArray()
                ->and($result)->toHaveCount(0);
        });

        it('only evaluates active rules.', function () {

            // Given
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'amount' => 150,
                    'currency' => 'USD',
                ],
                occurred_at: now()->format('Y-m-d H:i:s'),
            );

            $inactiveRule = new RewardRule(
                id: 1,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: false,
                conditions: [
                    [
                        'field' => 'amount',
                        'operator' => 'gte',
                        'value' => 50,
                    ],
                ],
            );

            $activeRule = new RewardRule(
                id: 2,
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'currency',
                        'operator' => 'eq',
                        'value' => 'USD',
                    ],
                ],
            );

            // When
            $repository = Mockery::mock(RewardRuleRepositoryInterface::class);
            $repository->shouldReceive('findActive')
                ->once()
                ->andReturn([$inactiveRule, $activeRule]);

            $useCase = new EvaluateRules($repository);

            $result = $useCase->execute($event);

            expect($result)->toHaveCount(1)
                ->and($result[0]->id())->toBe(2);
        });

    });

    describe('Negatives', function () {});

    describe('Edge Cases', function () {});

});
