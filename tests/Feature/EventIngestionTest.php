<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores a valid event', function () {

    // Arrange
    $payload = [
        'external_id' => 'evt_123',
        'source' => 'web',
        'type' => 'order.completed',
        'occurred_at' => now()->toISOString(),
        'payload' => [
            'order_id' => '1001',
            'customer_id' => '1',
        ],
    ];

    // Act
    $response = $this->postJson('/api/events', $payload);

    // Assert
    $response->assertStatus(201);

    $this->assertDatabaseHas('events', [
        'external_id' => $payload['external_id'],
        'source' => $payload['source'],
        'type' => $payload['type'],
    ]);

});
