<?php

use App\Application\UseCases\EvaluateRules;
use App\Domain\Events\Entities\Event;
use App\Domain\Rewards\Services\ConditionEngine;
use App\Domain\Rewards\Services\RewardEngine;
use App\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardIssueRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardRuleRepository;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Integration: Reward Issuance Idempotency', function () {

    describe('Positives', function () {});

    describe('Negatives', function () {

        it('does not issue duplicate reward when event is processed twice', function () {

            // Given
            EloquentRewardRule::create([
                'name' => 'Active Rule',
                'event_type' => 'order.completed',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([
                    [
                        'field' => 'amount',
                        'operator' => '>=',
                        'value' => 500,
                    ],
                ]),
                'priority' => 10,
            ]);

            // And
            $event = new Event(
                id: Str::uuid()->toString(),
                external_id : 'EXT-123',
                type : 'order.completed',
                source: 'shopify',
                payload: [
                    'amount' => 1000,
                ],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            $useCase = new EvaluateRules(
                new EloquentEventRepository,
                new EloquentRewardIssueRepository,
                new RewardEngine(
                    new EloquentRewardRuleRepository,
                    new ConditionEngine
                ),
            );

            $first = $useCase->execute($event);

            expect($first->already_evaluated)->toBeFalse()
                ->and($first->issued_rewards)->toBe(1);

            $second = $useCase->execute($event);

            expect($second->already_evaluated)->toBeTrue()
                ->and($second->issued_rewards)->toBe(0);

            $this->assertDatabaseCount('events', 1);
            $this->assertDatabaseCount('reward_issues', 1);

        });

    });

    describe('Edge Cases', function () {});
});
