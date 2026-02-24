<?php

use App\Domain\Events\Entities\Event;
use App\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use App\Models\Merchant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->merchant = Merchant::factory()->active()->create();
    $this->repository = new EloquentEventRepository;
});

describe('Integration: EloquentRewardRuleRepository', function () {

    it('exists returns false when event is not yet stored.', function () {

        $event = new Event(
            id: Str::uuid()->toString(),
            merchant_id: $this->merchant->id,
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'shopify',
            payload: ['amount' => 1000],
            occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            processed_at: null,
        );

        expect($this->repository->exists($event))->toBeFalse();
    });

    it('exists returns true after event is saved.', function () {

        $event = new Event(
            id: Str::uuid()->toString(),
            merchant_id: $this->merchant->id,
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'shopify',
            payload: ['amount' => 1000],
            occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            processed_at: null,
        );

        $this->repository->save($event);

        expect($this->repository->exists($event))->toBeTrue();
    });

    it('treats same external_id and source as already existing.', function () {

        $eventOne = new Event(
            id: Str::uuid()->toString(),
            merchant_id: $this->merchant->id,
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'shopify',
            payload: [],
            occurred_at: new DateTimeImmutable,
            processed_at: null,
        );

        $eventTwo = new Event(
            id: Str::uuid()->toString(),
            merchant_id: $this->merchant->id,
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'shopify',
            payload: [],
            occurred_at: new DateTimeImmutable,
            processed_at: null,
        );

        $this->repository->save($eventOne);

        expect($this->repository->exists($eventTwo))->toBeTrue();

        $this->assertDatabaseCount('events', 1);
    });

});
