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

describe('Feature: Reward Rule Selection', function () {

    describe('Positives', function () {

        it('should return selected reward rule by id when using /api/v1/rules/{id} get api endpoint.', function () {

            // Arrange:
            $rule = EloquentRewardRule::factory()->create([
                'merchant_id' => $this->merchant->id,
            ]);

            // Act:
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->get('/api/v1/rules/'.$rule->id);

            $response->assertOk()
                ->assertJsonStructure([
                    'data',
                ]);
        });

    });
});
