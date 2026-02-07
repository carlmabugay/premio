<?php

use App\Models\RewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

describe('Reward Issuance', function () {

    describe('Payload & Process', function () {

        it('ingests event and issues reward', function () {

            RewardRule::create([
                'id' => Str::uuid()->toString(),
                'event_type' => 'order.completed',
                'reward_type' => 'points',
                'points' => 100,
                'is_active' => true,
            ]);

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

            $response = $this->postJson('/api/v1/events', $payload);

            $response->assertStatus(201);

            $this->assertDatabaseHas('events', [
                'external_id' => $payload['external_id'],
                'type' => $payload['type'],
                'source' => $payload['source'],
            ]);

            $this->assertDatabaseHas('reward_ledger_entries', [
                'subject_type' => 'customer',
                'subject_id' => 'CST123',
                'points' => 100,
            ]);
        });

    });

    describe('Validation', function () {});
});
