<?php

use App\Domain\Rewards\Entities\RewardIssue;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardIssueRepository;
use App\Models\Event as EloquentEvent;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Integration: EloquentRewardIssueRepository', function () {

    it('prevents duplicate reward issue for same event and rule', function () {

        $event = EloquentEvent::factory()->create();
        $rule = EloquentRewardRule::factory()->create();

        $repository = new EloquentRewardIssueRepository;

        $issue = new RewardIssue(
            event_id: $event->id,
            reward_rule_id: $rule->id,
            reward_type: 'points',
            reward_value: 100.00
        );

        $repository->issue($issue);

        $this->assertDatabaseHas('reward_issues', [
            'event_id' => $event->id,
            'reward_rule_id' => $rule->id,
            'reward_type' => 'points',
            'reward_value' => 100.00,
        ]);
    });
});
