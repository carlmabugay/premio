<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Event Ingestion Feature', function () {

    describe('Positives', function () {

        it('saves when event payload is valid.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(201);

            $response->assertJsonStructure([
                'data' => [
                    'event_id',
                ],
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
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $firstResponse = $this->postJson('/api/v1/events', $payload)->assertStatus(201);
            $secondResponse = $this->postJson('/api/v1/events', $payload)->assertStatus(200);

            $firstResponse->assertJsonStructure([
                'data' => [
                    'event_id',
                ],
            ]);

            $secondResponse->assertJsonStructure([
                'data' => [
                    'event_id',
                ],
            ]);

            // Then
            $this->assertDatabaseCount('events', 1);
        });

    });

    describe('Negatives', function () {

        it('fails when external_id field is missing.', function () {

            // Given
            $payload = [
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('external_id');

        });

        it('fails when type field is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('type');

        });

        it('fails when source is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('source');

        });

        it('fails when payload is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('payload');

        });

        it('fails when payload structure is invalid.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'source' => 'shopify',
                'type' => 'order.completed',
                'payload' => 'invalid',
                'occurred_at' => now()->format('Y-m-d H:i:s'),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('payload');
        });

        it('fails when occurred at is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('occurred_at');

        });

        it('fails when occurred at timestamps is invalid.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => '2026-13-99',
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422)
                ->assertJsonValidationErrors('occurred_at');

        });
    });

});
