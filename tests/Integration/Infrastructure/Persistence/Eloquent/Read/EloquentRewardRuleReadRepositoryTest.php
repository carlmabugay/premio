<?php

use App\Domain\Rewards\Entities\RewardRule;
use App\Infrastructure\Persistence\Eloquent\Read\EloquentRewardRuleReadRepository;
use App\Models\Merchant as EloquentMerchant;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->merchant = EloquentMerchant::factory()->active()->create();
    $this->repository = new EloquentRewardRuleReadRepository;
});

describe('Integration: EloquentRewardRuleReadRepository', function () {

    describe('Positives', function () {

        it('should only return active reward rules when using findActive method.', function () {

            // Arrange:
            // Active rule
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
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
                'merchant_id' => $this->merchant->id,
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

            // Act:
            $rules = $this->repository->findActive('order.created');

            // Assert:
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

        it('should filter by event_type correctly when using findActive method.', function () {

            // Arrange:
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'Order Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'Signup Rule',
                'event_type' => 'user.registered',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            $rules = $this->repository->findActive('order.created');

            expect($rules[0])->toBeInstanceOf(RewardRule::class)
                ->and($rules)->toHaveCount(1)
                ->and($rules[0]->eventType())->toBe('order.created');

        });

        it('should respect priority ordering (ascending) when using findActive method.', function () {

            // Arrange:
            // Low priority rule
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'Low Priority Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 50,
            ]);

            // High priority rule
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'High Priority Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 10,
            ]);

            // Medium priority rule
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'Mid Priority Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 30,
            ]);

            // Act:
            $rules = $this->repository->findActive('order.created');

            // Assert:
            expect($rules)->toHaveCount(3)
                ->and($rules[0]->priority())->toBe(10)
                ->and($rules[1]->priority())->toBe(30)
                ->and($rules[2]->priority())->toBe(50);

        });

        it('should correctly hydrates DateTimeImmutable when using findActive method.', function () {

            // Arrange:
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'Date Rule',
                'event_type' => 'order.created',
                'is_active' => true,
                'starts_at' => '2026-01-01 10:00:00',
                'ends_at' => '2026-01-05 18:30:00',
                'conditions' => json_encode([]),
                'priority' => 50,
            ]);

            // Act:
            $rules = $this->repository->findActive('order.created');

            // Assert:
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

        it('should return all reward rules when using fetchAll method.', function () {

            // Arrange:
            // Merchant Rule
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
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

            $otherMerchant = EloquentMerchant::factory()->create();

            // Non-Merchant rule
            EloquentRewardRule::create([
                'merchant_id' => $otherMerchant->id,
                'name' => 'Active Rule',
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

            // Act:
            $rules = $this->repository->fetchAll($this->merchant->id);

            // Assert:
            expect($rules)->toHaveCount(1);
        });

        it('should filter by id correctly when using fetchById method.', function () {

            // Arrange:
            // Active rule
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
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
                'merchant_id' => $this->merchant->id,
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
            // Act:
            $rule = $this->repository->fetchById(EloquentRewardRule::first()->id);

            // Assert:
            expect($rule)->toBeInstanceOf(RewardRule::class)
                ->and($rule->merchantId())->toBe($this->merchant->id);
        });

    });

    describe('Negatives', function () {

        it('should return empty array when no active reward rules exist after using findActive method.', function () {

            // Arrange:
            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'Inactive Rule',
                'event_type' => 'order.created',
                'is_active' => false,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            EloquentRewardRule::create([
                'merchant_id' => $this->merchant->id,
                'name' => 'Different Event Rule',
                'event_type' => 'user.registered',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([]),
                'priority' => 1,
            ]);

            $rules = $this->repository->findActive('order.created');

            expect($rules)->toBeArray()
                ->and($rules)->toBeEmpty();

        });

    });

});
