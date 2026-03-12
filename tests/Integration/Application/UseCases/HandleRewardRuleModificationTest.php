<?php

use App\Application\UseCases\HandleRewardRuleModification;
use App\Domain\ApiKeys\Entities\ApiKey;
use App\Domain\ApiKeys\Services\ApiKeyService;
use App\Domain\Rewards\Services\RewardRuleService;
use App\Models\ApiKey as EloquentApiKey;
use App\Models\Merchant as EloquentMerchant;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->merchant = EloquentMerchant::factory()->active()->create();
    $this->api = EloquentApiKey::factory()->create([
        'merchant_id' => $this->merchant->id,
    ]);
});

describe('Integration: Handle Reward Rule Modification', function () {

    describe('Positive', function () {

        it('should update existing reward rule when using handle method.', function () {

            // Arrange:
            $ruleService = Mockery::mock(RewardRuleService::class);
            $apiKeyService = Mockery::mock(ApiKeyService::class);
            $useCase = new HandleRewardRuleModification($ruleService, $apiKeyService);

            $entityApiKey = new ApiKey(
                merchant_id: $this->api->merchant_id,
                name: $this->api->name,
                key_hash: $this->api->key_hash,
                is_active: $this->api->is_active,
            );

            $rule = EloquentRewardRule::factory()->create([
                'merchant_id' => $this->merchant->id,
            ]);

            $dataToUpdate = [
                'event_type' => 'cart.checkout.completed',
                'reward_type' => 'percentage',
                'reward_value' => 1,
            ];

            // Assert (Expectation):
            $apiKeyService->shouldReceive('fetchByApiKey')
                ->once()
                ->with($this->api->key_hash)
                ->andReturn($entityApiKey);

            $ruleService->shouldReceive('update')
                ->once()
                ->withArgs([$entityApiKey->merchantId(), $rule->id, $dataToUpdate])
                ->andReturn(1);

            // Act:
            $result = $useCase->handle($this->api->key_hash, $rule->id, $dataToUpdate);

            expect($result)->toBe(1);
        });

    });

});
