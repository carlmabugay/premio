<?php

use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;
use App\Infrastructure\Persistence\Eloquent\Read\EloquentRewardRuleReadRepository;
use App\Infrastructure\Persistence\Eloquent\Write\EloquentRewardRuleWriteRepository;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->readRepository = Mockery::mock(EloquentRewardRuleReadRepository::class);
    $this->writeRepository = Mockery::mock(EloquentRewardRuleWriteRepository::class);
    $this->service = new RewardRuleService($this->writeRepository, $this->readRepository);
});

describe('Unit: RewardRuleService', function () {

    describe('Positives', function () {

        it('should save a new reward rule when using save method.', function () {

            // Arrange:
            $rule = new RewardRule(
                merchant_id: Str::uuid()->toString(),
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

            // Assert (Expectation):
            $this->writeRepository->shouldReceive('save')
                ->once()
                ->with(Mockery::on(fn (RewardRule $rewardRule) => $rewardRule->name() === $rule->name()));

            // Act:
            $this->service->save($rule);
        });

        it('should return all reward rules when using fetchAll method.', function () {

            // Arrange:
            $rule = new RewardRule(
                merchant_id: Str::uuid()->toString(),
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                id: uniqid(),
            );

            // Assert (Expectation):
            $this->readRepository->shouldReceive('fetchAll')
                ->once()
                ->andReturn([
                    $rule,
                ]);

            // Act:
            $this->service->fetchAll($rule);

        });

        it('should return a reward rule when using fetchById method.', function () {

            // Arrange:
            $rule_id = 1;
            $rule = new RewardRule(
                merchant_id: Str::uuid()->toString(),
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
                id: $rule_id,
            );

            // Assert (Expectation):
            $this->readRepository->shouldReceive('fetchById')
                ->once()
                ->with($rule_id)
                ->andReturn($rule);

            // Act:
            $this->service->fetchById($rule_id);

        });

        it('should update an existing reward rule when using update method.', function () {

            // Arrange:
            $rule_id = 1;

            $dataToUpdate = [
                'event_type' => 'cart.checkout.completed',
                'reward_type' => 'percentage',
                'reward_value' => 1,
            ];

            // Assert (Expectation):
            $this->writeRepository->shouldReceive('update')
                ->once()
                ->withArgs([$rule_id, $dataToUpdate])
                ->andReturn(1);

            // Act:
            $this->service->update($rule_id, $dataToUpdate);

        });
    });
});
