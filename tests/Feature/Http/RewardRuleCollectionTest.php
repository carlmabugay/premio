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

describe('Feature: Reward Rule Collection', function () {

    describe('Positives', function () {

        it('should collect all reward rules when using /api/v1/rules get api endpoint.', function () {

            // Arrange:
            $rules = EloquentRewardRule::factory()->count(10)->create();

            // Act:
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->get('/api/v1/rules');

            $response->assertOk()
                ->assertJsonStructure([
                    'data',
                    'total',
                ]);
        });

    });
});
