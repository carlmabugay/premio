<?php

use App\Application\UseCases\HandleRewardRuleSelection;
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

describe('Integration: Reward Rule Selection', function () {

    describe('Positives', function () {

        it('should return a selected reward rule by id when using handle method.', function () {

            // Arrange:
            $rule = EloquentRewardRule::factory()->create([
                'merchant_id' => $this->merchant->id,
            ]);

            $ruleEntity = new RewardRule(
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
            );

            $service = Mockery::mock(RewardRuleService::class);
            $useCase = new HandleRewardRuleSelection($service);

            // Assert (Expectation):
            $service->shouldReceive('fetchById')
                ->once()
                ->with($rule->id)
                ->andReturn($ruleEntity);

            // Act:
            $result = $useCase->handle($rule->id);

            expect($result)->tobeInstanceOf(RewardRule::class)
                ->and($result->id())->toBe($ruleEntity->id())
                ->and($result->merchantId())->toBe($ruleEntity->merchantId())
                ->and($result->name())->toBe($ruleEntity->name());
        });

    });

});
