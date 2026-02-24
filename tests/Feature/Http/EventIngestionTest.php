<?php

use App\Models\ApiKey;
use App\Models\Event as EloquentEvent;
use App\Models\Merchant;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->merchant = Merchant::factory()->active()->create();
    $this->api = ApiKey::factory()->create([
        'merchant_id' => $this->merchant->id,
    ]);
});

describe('Feature: Event Ingestion', function () {

    describe('Positives', function () {

        it('processes an event and issues rewards.', function () {

            EloquentRewardRule::factory()->create([
                'event_type' => 'order.completed',
                'reward_type' => 'fixed',
                'reward_value' => 100,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'conditions' => json_encode([
                    [
                        'field' => 'order_total',
                        'operator' => '>=',
                        'value' => 100,
                    ],
                ]),
            ]);

            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'order_total' => 1500,
                ],
                'occurred_at' => '2026-01-01 12:00:00',
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            $event = EloquentEvent::first();

            // Then
            $response->assertCreated()
                ->assertJsonStructure([
                    'status',
                    'event' => [
                        'id',
                        'external_id',
                        'type',
                        'source',
                    ],
                    'rewards' => [
                        [
                            'rule_id',
                            'type',
                            'value',
                        ],
                    ],
                    'issued_rewards',
                ]);

            $this->assertDatabaseCount('events', 1);
            $this->assertDatabaseCount('reward_issues', 1);
        });

        it('saves when event payload is valid.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertCreated()
                ->assertJsonStructure([
                    'status',
                    'event' => [
                        'id',
                        'external_id',
                        'type',
                        'source',
                    ],
                    'rewards' => [],
                    'issued_rewards',
                ]);

            $this->assertDatabaseHas('events', [
                'external_id' => $payload['external_id'],
                'type' => $payload['type'],
                'source' => $payload['source'],
            ]);
        });

        it('returns 200 when the same event is submitted twice.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $firstResponse = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload)->assertCreated();

            $secondResponse = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload)->assertOk();

            $firstResponse->assertJsonStructure([
                'status',
                'event' => [
                    'id',
                    'external_id',
                    'type',
                    'source',
                ],
                'rewards' => [],
                'issued_rewards',
            ]);

            $secondResponse->assertJsonStructure([
                'status',
                'event' => [
                    'external_id',
                    'type',
                    'source',
                ],
                'issued_rewards',
            ]);

            // Then
            $this->assertDatabaseCount('events', 1);
        });

    });

    describe('Negatives', function () {

        it('fails when external_id field is missing.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('external_id');

        });

        it('fails when type field is missing.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('type');

        });

        it('fails when source is missing.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('source');

        });

        it('fails when payload is missing.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('payload');

        });

        it('fails when payload structure is invalid.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'source' => 'shopify',
                'type' => 'order.completed',
                'payload' => 'invalid',
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('payload');
        });

        it('fails when occurred at is missing.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
            ];

            // When
            $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('occurred_at');

        });

        it('fails when occurred at timestamps is invalid.', function () {

            // Given
            $payload = [
                'merchant_id' => $this->merchant->id,
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => '2026-13-99',
            ];

            // When
            $response = $response = $this->withHeaders([
                'X-API-KEY' => $this->api->key_hash,
            ])->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('occurred_at');

        });
    });

});
