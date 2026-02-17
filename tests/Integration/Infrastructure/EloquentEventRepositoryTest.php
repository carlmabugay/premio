<?php

use App\Domain\Events\Entities\Event;
use App\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('EloquentRewardRuleRepository Integration', function () {

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

            $repository = new EloquentEventRepository;

            expect($repository->exists($event))->toBeFalse();
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

            $repository = new EloquentEventRepository;

            $repository->save($event);

            expect($repository->exists($event))->toBeTrue();
        });

        it('prevents duplicate persistence when using same external_id.', function () {

            $repository = new EloquentEventRepository;

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

            $repository->save($eventOne);

            expect($repository->exists($eventTwo))->toBeTrue();

            $this->assertDatabaseCount('events', 1);
        });
    });

});
