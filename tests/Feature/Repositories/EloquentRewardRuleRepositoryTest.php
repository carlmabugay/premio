<?php

use App\Infrastructure\Persistence\Eloquent\EloquentRewardRuleRepository;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('EloquentRewardRuleRepository Feature', function () {

    describe('Positives', function () {
        it('findActive returns only active rules', function () {

            EloquentRewardRule::create([
                'name' => 'Active Rule',
                'event_type' => 'order.created',
                'conditions' => json_encode([
                    [
                        'field' => 'amount',
                        'operator' => '>=',
                        'value' => 100,
                    ],
                ]),
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'priority' => 10,
            ]);

            // Inactive rule
            EloquentRewardRule::create([
                'name' => 'Inactive Rule',
                'event_type' => 'order.created',
                'conditions' => json_encode([
                    [
                        'field' => 'amount',
                        'operator' => '>=',
                        'value' => 200,
                    ],
                ]),
                'is_active' => false,
                'starts_at' => null,
                'ends_at' => null,
                'priority' => 20,
            ]);

            $repository = new EloquentRewardRuleRepository;

            $rules = $repository->findActive('order.created');

            expect($rules)->toHaveCount(1)
                ->and($rules[0]->eventType())->toBe('order.created')
                ->and($rules[0]->isActive())->toBeTrue();
        });
    });

    describe('Negatives', function () {});

    describe('Edge Cases', function () {});

});
