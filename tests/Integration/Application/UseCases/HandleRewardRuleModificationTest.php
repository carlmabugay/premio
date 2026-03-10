<?php

use App\Application\UseCases\HandleRewardRuleModification;
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
            $service = Mockery::mock(RewardRuleService::class);
            $rule = EloquentRewardRule::factory()->create([
                'merchant_id' => $this->merchant->id,
            ]);

            $dataToUpdate = [
                'event_type' => 'cart.checkout.completed',
                'reward_type' => 'percentage',
                'reward_value' => 1,
            ];

            $useCase = new HandleRewardRuleModification($service);

            // Assert (Expectation):
            $service->shouldReceive('update')
                ->once()
                ->withArgs([$rule->id, $dataToUpdate])
                ->andReturn(1);

            // Act:
            $result = $useCase->handle($rule->id, $dataToUpdate);

            expect($result)->toBe(1);
        });

    });

});
