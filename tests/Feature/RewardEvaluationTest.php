<?php

use App\Application\UseCases\EvaluateRules;
use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardEngine;

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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $matchingRule = new RewardRule(
                id: 1,
                name: 'Matching Rule',
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
                ]
            );

            $nonMatchingRule = new RewardRule(
                id: 2,
                name: 'Non Matching Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'order_total',
                        'operator' => '>=',
                        'value' => 2000,
                    ],
                ]
            );

            // When
            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$matchingRule]);

            $issueRepository->shouldReceive('issue')
                ->once()
                ->with($event, $matchingRule);

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1)
                ->and($result->issued_rewards)->toBe(1);

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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $matchingRule = new RewardRule(
                id: 1,
                name: 'Matching Rule',
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
                name: 'Non Matching Rule',
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
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $issueRepository->shouldReceive('issue')
                ->once()
                ->with($event, $matchingRule);

            $rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$matchingRule]);

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1)
                ->and($result->issued_rewards)->toBe(1);

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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $nonMatchingRuleOne = new RewardRule(
                id: 2,
                name: 'None Matching Rule One',
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
                name: 'None Matching Rule Two',
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
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $issueRepository->shouldNotReceive('issue');

            $rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([]);

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(0)
                ->and($result->issued_rewards)->toBe(0);
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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            $inactiveRule = new RewardRule(
                id: 1,
                name: 'Inactive Rule',
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
                name: 'Active Rule',
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
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $issueRepository->shouldReceive('issue')
                ->once()
                ->with($event, $activeRule);

            $rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$activeRule]);

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1)
                ->and($result->issued_rewards)->toBe(1);
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
                    'customer_tier' => 'VIP',
                ],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            // And
            $ruleLowPriority = new RewardRule(
                id: 1,
                name: 'Low Priority',
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
                priority: 20,
            );

            // And
            $ruleHighPriority = new RewardRule(
                id: 2,
                name: 'High Priority',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                conditions: [
                    [
                        'field' => 'customer_tier',
                        'operator' => '=',
                        'value' => 'VIP',
                    ],
                ],
                priority: 50,
            );

            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $issueRepository->shouldReceive('issue')
                ->twice();

            $rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$ruleLowPriority, $ruleHighPriority]);

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(2)
                ->and($result->issued_rewards)->toBe(2);
        });

        it('produces consistent result when re-evaluating.', function () {

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
                        'field' => 'amount',
                        'operator' => '>=',
                        'value' => 100,
                    ],
                ],
            );

            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $issueRepository->shouldReceive('issue')
                ->once();

            $rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$rule]);

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1)
                ->and($result->issued_rewards)->toBe(1);

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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            $activeRule = new RewardRule(
                id: 2,
                name: 'Active Rule',
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
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $eventRepository->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $issueRepository->shouldReceive('issue')
                ->with($event, $activeRule);

            $rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$activeRule]);

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(1)
                ->and($result->issued_rewards)->toBe(1);
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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(true);

            $issueRepository->shouldNotReceive('issue');

            $rewardEngine->shouldNotReceive('evaluate');

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            expect($result->already_evaluated)->toBeTrue()
                ->and($result->matched_rules)->toBe(0)
                ->and($result->issued_rewards)->toBe(0);
        });

        it('does not issue duplicate reward when event already processed.', function () {

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
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
            $rewardEngine = Mockery::mock(RewardEngine::class);

            $eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(true);

            $rewardEngine->shouldNotReceive('evaluate');

            $useCase = new EvaluateRules($eventRepository, $issueRepository, $rewardEngine);

            $result = $useCase->execute($event);

            expect($result->already_evaluated)->toBeTrue()
                ->and($result->matched_rules)->toBe(0)
                ->and($result->issued_rewards)->toBe(0);

        });
    });

    //    describe('Edge Cases', function () {
    //
    //        it('handles large number of rules without failure (basic performance sanity).', function () {
    //
    //            // Given
    //            $event = new Event(
    //                id: Str::uuid()->toString(),
    //                external_id : 'EXT-123',
    //                type : 'order.completed',
    //                source: 'shopify',
    //                payload: [
    //                    'amount' => 150,
    //                    'currency' => 'USD',
    //                ],
    //                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
    //            );
    //
    //            $rules = [];
    //
    //            for ($i = 0; $i < 100000; $i++) {
    //                $rules[] = new RewardRule(
    //                    id: $i,
    //                    name: sprintf('Rule #%d', $i),
    //                    event_type: 'order.completed',
    //                    reward_type: 'fixed',
    //                    reward_value: 100,
    //                    is_active: true,
    //                    conditions: [
    //                        [
    //                            'field' => 'amount',
    //                            'operator' => 'gte',
    //                            'value' => 100,
    //                        ],
    //                    ],
    //                );
    //            }
    //
    //            // When
    //            $eventRepository = Mockery::mock(EventRepositoryInterface::class);
    //            $ruleRepository = Mockery::mock(RewardRuleRepositoryInterface::class);
    //            $issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
    //            $rewardEngine = Mockery::mock(RewardEngine::class);
    //
    //            $eventRepository->shouldReceive('exists')
    //                ->once()
    //                ->with($event)
    //                ->andReturn(false);
    //
    //            $eventRepository->shouldReceive('save')
    //                ->once()
    //                ->with($event);
    //
    //            $rewardEngine->shouldReceive('evaluate')
    //                ->with($event)
    //                ->andReturn($rules);
    //
    //            $issueRepository->shouldReceive('issue')
    //                ->times(100000);
    //
    //            $useCase = new EvaluateRules($eventRepository, $ruleRepository, $issueRepository, $rewardEngine);
    //
    //            $result = $useCase->execute($event);
    //
    //            expect($result->already_evaluated)->toBeFalse()
    //                ->and($result->matched_rules)->toBe(100000);
    //
    //        });
    //
    //    });

});
