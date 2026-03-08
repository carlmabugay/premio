<?php

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Application\DTOs\Write\RewardRuleCreateDTO;
use App\Application\UseCases\HandleRewardRuleCreation;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;
use Illuminate\Support\Str;

describe('Unit: Handle Reward Rule Creation', function () {

    describe('Positive', function () {

        it('should create a new reward rule when using handle method.', function () {

            // Arrange:
            $service = Mockery::mock(RewardRuleService::class);
            $useCase = new HandleRewardRuleCreation($service);

            $dto = RewardRuleCreateDTO::fromArray([
                'merchant_id' => Str::uuid()->toString(),
                'name' => 'New Active Rule',
                'event_type' => 'order.completed',
                'reward_type' => 'fixed',
                'reward_value' => 100,
                'is_active' => true,
                'starts_at' => '2026-01-01',
                'ends_at' => '2026-05-01',
                'conditions' => [
                    [
                        'field' => 'amount',
                        'operator' => '>=',
                        'value' => 100,
                    ],
                ],
                'priority' => 10,
            ]);

            $rule = new RewardRule(
                merchant_id: $dto->merchant_id,
                name: $dto->name,
                event_type: $dto->event_type,
                reward_type: $dto->reward_type,
                reward_value: $dto->reward_value,
                is_active: $dto->is_active,
                starts_at: $dto->starts_at,
                ends_at: $dto->ends_at,
                id: 1,
            );

            // Assert (Expectation):
            $service->shouldReceive('save')
                ->once()
                ->andReturn($rule);

            // Act:
            $result = $useCase->handle($dto);

            expect($result)->toBeInstanceOf(RewardRuleReadDTO::class)
                ->and($result->merchant_id === $dto->merchant_id)
                ->and($result->name === $dto->name);
        });

    });

});
