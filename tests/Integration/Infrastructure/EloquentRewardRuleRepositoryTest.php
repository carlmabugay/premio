<?php

use App\Domain\Rewards\Entities\RewardRule;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardRuleRepository;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Integration: EloquentRewardRuleRepository', function () {

    describe('Positives', function () {

        it('findActive returns only active rules.', function () {

            EloquentRewardRule::create([
                'name' => 'Active Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([
                    [
                        'field' => 'amount',
                        'operator' => '>=',
                        'value' => 100,
                    ],
                ]),
                'priority' => 10,
            ]);

            // Inactive rule
            EloquentRewardRule::create([
                'name' => 'Inactive Rule',
                'event_type' => 'order.created',
                'is_active' => false,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([
                    [
                        'field' => 'amount',
                        'operator' => '>=',
                        'value' => 200,
                    ],
                ]),
                'priority' => 20,
            ]);

            $repository = new EloquentRewardRuleRepository;

            $rules = $repository->findActive('order.created');

            expect($rules[0])->toBeInstanceOf(RewardRule::class)
                ->and(get_object_vars($rules[0]->conditions()[0]))->toBe([
                    'field' => 'amount',
                    'operator' => '>=',
                    'value' => 100,
                ])
                ->and($rules)->toHaveCount(1)
                ->and($rules[0]->eventType())->toBe('order.created')
                ->and($rules[0]->isActive())->toBeTrue();

        });

        it('findActive filters by event_type correctly.', function () {

            EloquentRewardRule::create([
                'name' => 'Order Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            EloquentRewardRule::create([
                'name' => 'Signup Rule',
                'event_type' => 'user.registered',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            $repository = new EloquentRewardRuleRepository;

            $rules = $repository->findActive('order.created');

            expect($rules[0])->toBeInstanceOf(RewardRule::class)
                ->and($rules)->toHaveCount(1)
                ->and($rules[0]->eventType())->toBe('order.created');

        });

        it('findActive respects priority ordering (ascending).', function () {

            EloquentRewardRule::create([
                'name' => 'Low Priority Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 50,
            ]);

            EloquentRewardRule::create([
                'name' => 'High Priority Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 10,
            ]);

            EloquentRewardRule::create([
                'name' => 'Mid Priority Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 30,
            ]);

            $repository = new EloquentRewardRuleRepository;

            $rules = $repository->findActive('order.created');

            expect($rules)->toHaveCount(3)
                ->and($rules[0]->priority())->toBe(10)
                ->and($rules[1]->priority())->toBe(30)
                ->and($rules[2]->priority())->toBe(50);

        });

        it('findActive correctly hydrates DateTimeImmutable.', function () {

            EloquentRewardRule::create([
                'name' => 'Date Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => '2026-01-01 10:00:00',
                'ends_at' => '2026-01-05 18:30:00',
                'conditions' => json_encode([]),
                'priority' => 50,
            ]);

            $repository = new EloquentRewardRuleRepository;

            $rules = $repository->findActive('order.created');

            expect($rules)->toHaveCount(1);

            $rule = $rules[0];

            expect($rule)->toBeInstanceOf(RewardRule::class)
                ->and($rule->startsAt())->toBeInstanceOf(DateTimeImmutable::class)
                ->and($rule->endsAt())->toBeInstanceOf(DateTimeImmutable::class)
                ->and($rule->startsAt()->format('Y-m-d H:i:s'))
                ->toBe('2026-01-01 10:00:00')
                ->and($rule->endsAt()->format('Y-m-d H:i:s'))
                ->toBe('2026-01-05 18:30:00');

        });

    });

    describe('Negatives', function () {

        it('findActive returns empty array when no active rules exist.', function () {

            EloquentRewardRule::create([
                'name' => 'Inactive Rule',
                'event_type' => 'order.created',
                'is_active' => false,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            EloquentRewardRule::create([
                'name' => 'Different Event Rule',
                'event_type' => 'user.registered',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            $repository = new EloquentRewardRuleRepository;

            $rules = $repository->findActive('order.created');

            expect($rules)->toBeArray()
                ->and($rules)->toBeEmpty();

        });

    });

});
