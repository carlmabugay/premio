<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Event Ingestion Feature', function () {

    describe('Payload & Process', function () {

        it('stores a valid event.', function () {

            // Arrange
            $payload = [
                'external_id' => 'EVT123',
                'source' => 'web',
                'type' => 'order.completed',
                'occurred_at' => now()->toISOString(),
                'payload' => [
                    'order_id' => '1001',
                    'customer_id' => '1',
                ],
            ];

            // Act
            $response = $this->postJson('/api/v1/events', $payload);

            // Assert
            $response->assertStatus(201);

            $this->assertDatabaseHas('events', [
                'external_id' => $payload['external_id'],
                'source' => $payload['source'],
                'type' => $payload['type'],
            ]);

        });

        it('stores the same external event only once.', function () {

            // Arrange
            $payload = [
                'external_id' => 'EVT123',
                'source' => 'web',
                'type' => 'order.completed',
                'occurred_at' => now()->toISOString(),
                'payload' => [
                    'order_id' => '1001',
                    'customer_id' => '1',
                ],
            ];

            // Act
            $this->postJson('/api/v1/events', $payload)->assertStatus(201);
            $this->postJson('/api/v1/events', $payload)->assertStatus(200);

            $this->assertDatabaseCount('events', 1);

            $this->assertDatabaseHas('events', [
                'external_id' => $payload['external_id'],
                'type' => $payload['type'],
                'source' => $payload['source'],
            ]);
        });

    });

    describe('Validations', function () {

        it('rejects missing required fields.', function () {

            // Arrange
            $payload = [];

            // Act
            $response = $this->postJson('/api/v1/events', $payload);

            // Assert
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The external id field is required. (and 4 more errors)',
                'errors' => [],
            ]);

            $response->assertJsonValidationErrors([
                'external_id',
                'source',
                'type',
                'payload',
                'occurred_at',
            ]);

            $this->assertDatabaseCount('events', 0);

        });

        it('rejects invalid timestamps.', function () {

            // Arrange
            $payload = [
                'external_id' => 'EVT123',
                'source' => 'web',
                'type' => 'order.completed',
                'occurred_at' => '01/Jan/2026',
                'payload' => [
                    'order_id' => '1001',
                    'customer_id' => '1',
                ],
            ];

            $response = $this->postJson('/api/v1/events', $payload);

            // Assert
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The occurred at field must be a valid date.',
                'errors' => [
                    'occurred_at' => [
                        'The occurred at field must be a valid date.',
                    ],
                ],
            ]);

            $response->assertJsonValidationErrors([
                'occurred_at',
            ]);

            $this->assertDatabaseCount('events', 0);
        });

        it('rejects invalid payload structure.', function () {

            // Arrange
            $payload = [
                'external_id' => 'EVT123',
                'source' => 'web',
                'type' => 'order.completed',
                'occurred_at' => now()->toISOString(),
                'payload' => 'invalid payload',
            ];

            $response = $this->postJson('/api/v1/events', $payload);

            // Assert
            $response->assertStatus(422);

            $response->assertJson([
                'message' => 'The payload field must be an array.',
                'errors' => [
                    'payload' => [
                        'The payload field must be an array.',
                    ],
                ],
            ]);

            $response->assertJsonValidationErrors([
                'payload',
            ]);

            $this->assertDatabaseCount('events', 0);
        });
    });

});
