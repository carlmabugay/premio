<?php

use App\Models\ApiKey as EloquentApiKey;
use App\Models\Merchant as EloquentMerchant;
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

        it('create a reward rule.', function () {

            $payload = [
                'merchant_id' => $this->merchant->id,
                'event_type' => 'order.completed',
                'name' => 'Active Rule',
                'reward_type' => 'fixed',
                'reward_value' => 100,
                'starts_at' => '2026-01-01',
                'ends_at' => '2026-05-01',
                'is_active' => true,
                'conditions' => [
                    [
                        'field' => 'order_total',
                        'operator' => '>=',
                        'value' => 100,
                    ],
                ],
                'priority' => 100,
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/rules', $payload);

            $response->assertStatus(200);

            $this->assertDatabaseHas('reward_rules', [
                'merchant_id' => $this->merchant->id,
                'event_type' => 'order.completed',
                'name' => 'Active Rule',
                'reward_type' => 'fixed',
                'reward_value' => 100,
                'starts_at' => '2026-01-01',
                'ends_at' => '2026-05-01',
                'is_active' => true,
                'priority' => 100,
            ]);

        });

    });
});
