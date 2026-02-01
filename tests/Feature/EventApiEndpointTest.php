<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('stores a valid event', function () {

    $payload = [
        'id' => '1',
        'type' => 'order.completed',
        'source' => 'web',
        'occurred_at' => now()->toISOString(),
        'payload' => [
            'order_id' => '1001',
            'customer_id' => '1',
        ],
    ];

    $this->postJson('/api/events', $payload)->assertCreated();

    $this->assertDatabaseHas('events', [
        'external_event_id' => 'evt_123',
        'event_type' => 'order.completed',
        'source' => 'web',
    ]);

});
