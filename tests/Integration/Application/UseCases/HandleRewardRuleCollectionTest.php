<?php

use App\Application\UseCases\HandleRewardRuleCollection;
use App\Domain\Rewards\Entities\RewardRule;
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

            $entityRules = $rules->map(fn ($rule) => new RewardRule(
                merchant_id: $rule->merchant_id,
                name: $rule->name,
                event_type: $rule->event_type,
                reward_type: $rule->reward_type,
                reward_value: $rule->reward_value,
                is_active: $rule->is_active,
                starts_at: $rule->starts_at ? new DateTimeImmutable($rule->starts_at) : null,
                ends_at: $rule->ends_at ? new DateTimeImmutable($rule->ends_at) : null,
                conditions: json_decode($rule->conditions),
                priority: $rule->priority,
                id: $rule->id,
            ))->all();

            $service = Mockery::mock(RewardRuleService::class);
            $useCase = new HandleRewardRuleCollection($service);

            // Assert (Expectation):
            $service->shouldReceive('fetchAll')
                ->once()
                ->andReturn($entityRules);

            // Act:
            $result = $useCase->handle();

            expect(count($result))->toBe(count($entityRules));
        });

    });

});
