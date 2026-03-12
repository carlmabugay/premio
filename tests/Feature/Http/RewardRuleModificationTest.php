<?php

use App\Models\ApiKey as EloquentApiKey;
use App\Models\Merchant as EloquentMerchant;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->merchant = EloquentMerchant::factory()->active()->create();
    $this->api = EloquentApiKey::factory()->create([
        'merchant_id' => $this->merchant->id,
    ]);
});

describe('Feature: Reward Rule Creation', function () {

    describe('Positives', function () {

        it('should create a new rule when using /api/v1/rules/{id} put api endpoint.', function () {

            // Arrange:
            $rule = EloquentRewardRule::factory()->create([
                'merchant_id' => $this->merchant->id,
            ]);

            $payload = [
                'id' => $rule->id,
                'name' => 'Rule that recently modified.',
            ];

            // Act:
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->putJson('/api/v1/rules/', $payload);

            // Assert:
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'status',
                    'data',
                ]);

            $this->assertDatabaseHas('reward_rules', $payload);

        });

    });

});
