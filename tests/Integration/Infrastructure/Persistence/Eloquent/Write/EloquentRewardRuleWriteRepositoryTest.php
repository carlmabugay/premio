<?php

namespace Tests\Integration\Infrastructure\Persistence\Eloquent\Write;

use App\Domain\Rewards\Entities\RewardRule;
use App\Infrastructure\Persistence\Eloquent\Write\EloquentRewardRuleWriteRepository;
use App\Models\Merchant as EloquentMerchant;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->merchant = EloquentMerchant::factory()->active()->create();
});

describe('Integration: EloquentRewardRuleWriteRepository', function () {

    describe('Positives', function () {

        it('should save a new reward rule when using save method.', function () {

            // Arrange:
            $rule = new RewardRule(
                merchant_id: $this->merchant->id,
                name: 'New Active Rule',
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
                ]
            );

            // Act:
            $repository = new EloquentRewardRuleWriteRepository;
            $repository->save($rule);

            // Assert:
            $this->assertDatabaseCount('reward_rules', 1);

        });

        it('should update an existing reward rule when using update method.', function () {

            // Arrange:
            $rule = EloquentRewardRule::factory()->create([
                'merchant_id' => $this->merchant->id,
            ]);

            $payload = [
                'id' => $rule->id,
                'event_type' => 'cart.checkout.completed',
                'reward_type' => 'percentage',
                'reward_value' => 1,
            ];

            // Act:
            $repository = new EloquentRewardRuleWriteRepository;
            $repository->update($this->merchant->id, $payload);

            // Assert:
            $this->assertDatabaseHas('reward_rules', $payload);

        });
    });
});
