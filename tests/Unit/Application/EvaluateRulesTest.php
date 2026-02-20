<?php

use App\Application\UseCases\EvaluateRules;
use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Entities\RewardIssue;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardEngine;
use App\Exceptions\DuplicateEvent;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->eventRepository = Mockery::mock(EventRepositoryInterface::class);
    $this->issueRepository = Mockery::mock(RewardIssueRepositoryInterface::class);
    $this->rewardEngine = Mockery::mock(RewardEngine::class);

    $this->useCase = new EvaluateRules($this->eventRepository, $this->issueRepository, $this->rewardEngine);
});

describe('Unit: Evaluate Rules', function () {

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
            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $this->eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $this->rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$matchingRule]);

            $this->issueRepository->shouldReceive('issue')
                ->once()
                ->with(Mockery::on(function ($issue) use ($event, $matchingRule) {

                    return $issue instanceof RewardIssue
                        && $issue->eventId() === $event->id()
                        && $issue->rewardRuleId() === $matchingRule->id()
                        && $issue->rewardType() === $matchingRule->rewardType()
                        && $issue->rewardValue() == $matchingRule->rewardValue();

                }));

            $result = $this->useCase->execute($event);

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
            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $this->eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $this->issueRepository->shouldReceive('issue')
                ->once()
                ->with(Mockery::on(function ($issue) use ($event, $matchingRule) {

                    return $issue instanceof RewardIssue
                        && $issue->eventId() === $event->id()
                        && $issue->rewardRuleId() === $matchingRule->id()
                        && $issue->rewardType() === $matchingRule->rewardType()
                        && $issue->rewardValue() == $matchingRule->rewardValue();

                }));

            $this->rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$matchingRule]);

            $result = $this->useCase->execute($event);

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
            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $this->eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $this->issueRepository->shouldNotReceive('issue');

            $this->rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([]);

            $result = $this->useCase->execute($event);

            // Then
            expect($result->already_evaluated)->toBeFalse()
                ->and($result->matched_rules)->toBe(0)
                ->and($result->issued_rewards)->toBe(0);
        });

        it('issues rewards for rules returned by the engine.', function () {

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
            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $this->eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $this->issueRepository->shouldReceive('issue')
                ->once()
                ->with(Mockery::on(function ($issue) use ($event, $activeRule) {

                    return $issue instanceof RewardIssue
                        && $issue->eventId() === $event->id()
                        && $issue->rewardRuleId() === $activeRule->id()
                        && $issue->rewardType() === $activeRule->rewardType()
                        && $issue->rewardValue() == $activeRule->rewardValue();

                }));

            $this->rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$activeRule]);

            $result = $this->useCase->execute($event);

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

            // When
            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $this->eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $this->issueRepository->shouldReceive('issue')
                ->once()
                ->ordered()
                ->with(Mockery::type(RewardIssue::class));

            $this->issueRepository->shouldReceive('issue')
                ->once()
                ->ordered()
                ->with(Mockery::type(RewardIssue::class));

            $this->rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$ruleLowPriority, $ruleHighPriority]);

            $result = $this->useCase->execute($event);

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

            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->andReturn(false);

            $this->eventRepository->shouldReceive('save')
                ->once()
                ->with($event);

            $this->issueRepository->shouldReceive('issue')
                ->once()
                ->with(Mockery::on(function ($issue) use ($event, $rule) {

                    return $issue instanceof RewardIssue
                        && $issue->eventId() === $event->id()
                        && $issue->rewardRuleId() === $rule->id()
                        && $issue->rewardType() === $rule->rewardType()
                        && $issue->rewardValue() == $rule->rewardValue();

                }));

            $this->rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$rule]);

            $result = $this->useCase->execute($event);

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
            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(false);

            $this->eventRepository->shouldReceive('save')
                ->once()
                ->with($event)
                ->andReturn(true);

            $this->issueRepository->shouldReceive('issue')
                ->once()
                ->with(Mockery::on(function ($issue) use ($event, $activeRule) {

                    return $issue instanceof RewardIssue
                        && $issue->eventId() === $event->id()
                        && $issue->rewardRuleId() === $activeRule->id()
                        && $issue->rewardType() === $activeRule->rewardType()
                        && $issue->rewardValue() == $activeRule->rewardValue();

                }));

            $this->rewardEngine->shouldReceive('evaluate')
                ->with($event)
                ->andReturn([$activeRule]);

            $result = $this->useCase->execute($event);

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

            // When
            $this->eventRepository->shouldReceive('exists')
                ->once()
                ->with($event)
                ->andReturn(true);

            $this->issueRepository->shouldNotReceive('issue');

            $this->rewardEngine->shouldNotReceive('evaluate');

            $result = $this->useCase->execute($event);

        })->throws(DuplicateEvent::class);

    });
});
