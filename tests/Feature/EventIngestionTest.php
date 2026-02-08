<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Event Ingestion Feature', function () {

    describe('Positive', function () {

        it('saves when event payload is valid.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.complete',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->toISOString(),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(201);

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
                'type' => 'order.complete',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->toISOString(),
            ];

            // When
            $this->postJson('/api/v1/events', $payload)->assertStatus(201);
            $this->postJson('/api/v1/events', $payload)->assertStatus(200);

            // Then
            $this->assertDatabaseCount('events', 1);
        });

    });

    describe('Negative', function () {

        it('fails when external_id field is missing.', function () {

            // Given
            $payload = [
                'type' => 'order.completed',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->toISOString(),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The external id field is required.',
                'errors' => [
                    'external_id' => ['The external id field is required.'],
                ],
            ]);

        });

        it('fails when type field is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->toISOString(),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The type field is required.',
                'errors' => [
                    'type' => ['The type field is required.'],
                ],
            ]);

        });

        it('fails when source is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.completed',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
                'occurred_at' => now()->toISOString(),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The source field is required.',
                'errors' => [
                    'source' => ['The source field is required.'],
                ],
            ]);
        });

        it('fails when payload is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.complete',
                'source' => 'shopify',
                'occurred_at' => now()->toISOString(),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The payload field is required.',
                'errors' => [
                    'payload' => ['The payload field is required.'],
                ],
            ]);

        });

        it('fails when payload structure is invalid.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'source' => 'shopify',
                'type' => 'order.completed',
                'payload' => 'invalid',
                'occurred_at' => now()->toISOString(),
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The payload field must be an array.',
                'errors' => [
                    'payload' => ['The payload field must be an array.'],
                ],
            ]);
        });

        it('fails when occurred at is missing.', function () {

            // Given
            $payload = [
                'external_id' => 'EXT-123',
                'type' => 'order.complete',
                'source' => 'shopify',
                'payload' => [
                    'customer_id' => 'CST-123',
                ],
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The occurred at field is required.',
                'errors' => [
                    'occurred_at' => ['The occurred at field is required.'],
                ],
            ]);
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
                'occurred_at' => 'invalid',
            ];

            // When
            $response = $this->postJson('/api/v1/events', $payload);

            // Then
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The occurred at field must be a valid date.',
                'errors' => [
                    'occurred_at' => ['The occurred at field must be a valid date.'],
                ],
            ]);
        });
    });

});
