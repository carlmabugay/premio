<?php

use App\Application\UseCases\EvaluateRules;
use App\Domain\Events\Contracts\EventRepositoryInterface;
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
            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldReceive('findActive')
                ->once()
                ->andReturn([$matchingRule, $nonMatchingRule]);

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1);

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
            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository
                ->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository
                ->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldReceive('findActive')
                ->once()
                ->andReturn([$matchingRule, $nonMatchingRule]);

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1);

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
            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository
                ->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository
                ->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldReceive('findActive')
                ->once()
                ->andReturn([$nonMatchingRuleOne, $nonMatchingRuleTwo]);

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(0);
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
            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository
                ->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository
                ->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldReceive('findActive')
                ->once()
                ->andReturn([$inactiveRule, $activeRule]);

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1);
        });

        it('evaluates rules in deterministic order (if order matters).', function () {

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

            $ruleLowPriority = Mockery::mock(RewardRule::class);
            $ruleLowPriority->priority = 20;
            $ruleLowPriority->shouldReceive('matches')
                ->once()
                ->ordered()
                ->andReturn(true);

            $ruleHighPriority = Mockery::mock(RewardRule::class);
            $ruleHighPriority->priority = 10;
            $ruleHighPriority->shouldReceive('matches')
                ->once()
                ->ordered()
                ->andReturn(true);

            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository
                ->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository
                ->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldReceive('findActive')
                ->once()
                ->andReturn([
                    $ruleLowPriority,
                    $ruleHighPriority,
                ]);

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(2);
        });

        it('handles large number of rules without failure (basic performance sanity).', function () {

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

            $rules = [];

            for ($i = 0; $i < 100000; $i++) {
                $rules[] = new RewardRule(
                    id: $i,
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
                    ],
                );
            }

            // When
            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository
                ->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository
                ->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldReceive('findActive')
                ->once()
                ->andReturn($rules);

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(100000);

        });

    });

    describe('Negatives', function () {

        it('does not evaluate inactive rules at all (repository filter).', function () {

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

            $activeRule = new RewardRule(
                id: 2,
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
                ],
            );

            // When
            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository
                ->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository
                ->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldReceive('findActive')
                ->once()
                ->andReturn([$activeRule]);

            $ruleRepository->shouldNotReceive('findAll');

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1);
        });

        it('does not evaluate rules twice for the same event.', function () {

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

            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);

            $eventRepository
                ->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(true);

            $ruleRepository->shouldNotReceive('findActive');

            $useCase = new EvaluateRules($eventRepository, $ruleRepository);

            $result = $useCase->execute($event);

            expect($result->already_evaluated)->toBeTrue()
                ->and($result->matched_rules)->toBe(0);
        });
    });

    describe('Edge Cases', function () {});

});
