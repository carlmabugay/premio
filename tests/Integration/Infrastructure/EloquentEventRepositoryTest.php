<?php

use App\Domain\Events\Entities\Event;
use App\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new EloquentEventRepository;
});

describe('Integration: EloquentRewardRuleRepository', function () {

    describe('Positives', function () {

        it('exists returns false when event is not yet stored.', function () {

            $event = new Event(
                id: Str::uuid()->toString(),
                external_id: 'EXT-123',
                type: 'order.completed',
                source: 'shopify',
                payload: ['amount' => 1000],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            expect($this->repository->exists($event))->toBeFalse();
        });

        it('exists returns true after event is saved.', function () {

            $event = new Event(
                id: Str::uuid()->toString(),
                external_id: 'EXT-123',
                type: 'order.completed',
                source: 'shopify',
                payload: ['amount' => 1000],
                occurred_at: new DateTimeImmutable('2026-01-01 12:00:00'),
            );

            $this->repository->save($event);

            expect($this->repository->exists($event))->toBeTrue();
        });
    });

    it('allows same external_id for different sources.', function () {

        $eventOne = new Event(
            id: Str::uuid()->toString(),
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'shopify',
            payload: [],
            occurred_at: new DateTimeImmutable,
        );

        $eventTwo = new Event(
            id: Str::uuid()->toString(),
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'stripe',
            payload: [],
            occurred_at: new DateTimeImmutable,
        );

        $this->repository->save($eventOne);
        $this->repository->save($eventTwo);

        $this->assertDatabaseCount('events', 2);
    });

    it('treats same external_id and source as already existing.', function () {

        $eventOne = new Event(
            id: Str::uuid()->toString(),
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'shopify',
            payload: [],
            occurred_at: new DateTimeImmutable,
        );

        $eventTwo = new Event(
            id: Str::uuid()->toString(),
            external_id: 'EXT-123',
            type: 'order.completed',
            source: 'shopify',
            payload: [],
            occurred_at: new DateTimeImmutable,
        );

        $this->repository->save($eventOne);

        expect($this->repository->exists($eventTwo))->toBeTrue();

        $this->assertDatabaseCount('events', 1);
    });
});
