<?php

use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;
use Illuminate\Support\Str;

describe('Unit: RewardRuleService', function () {

    describe('Positives', function () {

        it('should save reward rule.', function () {

            // Arrange:
            $repo = Mockery::mock(RewardRuleRepositoryInterface::class);
            $rule = new RewardRule(
                id: 1,
                merchant_id: Str::uuid()->toString(),
                name: 'Active Rule',
                event_type: 'order.completed',
                reward_type: 'fixed',
                reward_value: 100,
                is_active: true,
            );

            // Expectation / Assert:
            $repo->shouldReceive('save')
                ->once()
                ->with(Mockery::on(fn (RewardRule $rewardRule) => $rewardRule->name() === $rule->name()));

            // Act:
            $service = new RewardRuleService($repo);
            $service->save($rule);

        });
    });

    it('should fetch list of reward rules.', function () {

        // Arrange:
        $repo = Mockery::mock(RewardRuleRepositoryInterface::class);
        $rule = new RewardRule(
            id: 1,
            merchant_id: Str::uuid()->toString(),
            name: 'Active Rule',
            event_type: 'order.completed',
            reward_type: 'fixed',
            reward_value: 100,
            is_active: true,
        );

        // Expectation / Assert:
        $repo->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                $rule,
            ]);

        // Act:
        $service = new RewardRuleService($repo);
        $service->fetchAll($rule);
    });
});
