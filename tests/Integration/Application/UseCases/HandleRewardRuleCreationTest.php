<?php

use App\Application\DTOs\Read\RewardRuleReadDTO;
use App\Application\DTOs\Write\RewardRuleCreateDTO;
use App\Application\UseCases\HandleRewardRuleCreation;
use App\Domain\ApiKeys\Entities\ApiKey;
use App\Domain\ApiKeys\Services\ApiKeyService;
use App\Domain\Rewards\Entities\RewardRule;
use App\Domain\Rewards\Services\RewardRuleService;
use App\Models\ApiKey as EloquentApiKey;
use App\Models\Merchant as EloquentMerchant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->merchant = EloquentMerchant::factory()->active()->create();
    $this->api = EloquentApiKey::factory()->create([
        'merchant_id' => $this->merchant->id,
    ]);
});

describe('Integration: Handle Reward Rule Creation', function () {

    describe('Positive', function () {

        it('should create a new reward rule when using handle method.', function () {

            // Arrange:
            $ruleService = Mockery::mock(RewardRuleService::class);
            $apiKeyService = Mockery::mock(ApiKeyService::class);
            $useCase = new HandleRewardRuleCreation($ruleService, $apiKeyService);

            $entityApiKey = new ApiKey(
                merchant_id: $this->api->merchant_id,
                name: $this->api->name,
                key_hash: $this->api->key_hash,
                is_active: $this->api->is_active,
            );

            $dto = RewardRuleCreateDTO::fromArray([
                'merchant_id' => $entityApiKey->merchantId(),
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

            $entityRule = new RewardRule(
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
            $apiKeyService->shouldReceive('fetchByApiKey')
                ->once()
                ->with($this->api->key_hash)
                ->andReturn($entityApiKey);

            $ruleService->shouldReceive('save')
                ->once()
                ->andReturn($entityRule);

            // Act:
            $result = $useCase->handle($this->api->key_hash, $dto);

            expect($result)->toBeInstanceOf(RewardRuleReadDTO::class)
                ->and($result->merchant_id === $dto->merchant_id)
                ->and($result->name === $dto->name);
        });

    });

});
