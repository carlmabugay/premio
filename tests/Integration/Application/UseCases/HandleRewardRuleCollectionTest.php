<?php

use App\Application\UseCases\HandleRewardRuleCollection;
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

describe('Integration: Reward Rule Collection', function () {

    describe('Positives', function () {

        it('should collect all reward rules when using handle method.', function () {

            // Arrange:
            $rules = EloquentRewardRule::factory()->count(10)->create([
                'merchant_id' => $this->merchant->id,
            ]);

            $service = Mockery::mock(RewardRuleService::class);
            $useCase = new HandleRewardRuleCollection($service);

            // Assert (Expectation):
            $service->shouldReceive('fetchAll')
                ->once()
                ->andReturn([$rules]);

            // Act:
            $result = $useCase->handle();

            expect($result[0]->count())->toBe($rules->count());
        });

    });

});
