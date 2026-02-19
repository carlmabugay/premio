<?php

use App\Domain\Rewards\Entities\RewardIssue;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardIssueRepository;
use App\Models\Event as EloquentEvent;
use App\Models\RewardRule as EloquentRewardRule;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Integration: EloquentRewardIssueRepository', function () {

    it('prevents duplicate reward issue for same event and rule.', function () {

        $event = EloquentEvent::factory()->create();
        $rule = EloquentRewardRule::factory()->create();

        $repository = new EloquentRewardIssueRepository;

        $repository->issue(
            new RewardIssue(
                event_id: $event->id,
                reward_rule_id: $rule->id,
                reward_type: 'points',
                reward_value: 100.00,
            )
        );

        $this->assertDatabaseHas('reward_issues', [
            'event_id' => $event->id,
            'reward_rule_id' => $rule->id,
            'reward_type' => 'points',
            'reward_value' => 100.00,
        ]);
    });

    it('allows multiple rules for same event.', function () {

        $event = EloquentEvent::factory()->create();
        $ruleOne = EloquentRewardRule::factory()->create();
        $ruleTwo = EloquentRewardRule::factory()->create();

        $repository = new EloquentRewardIssueRepository;

        $repository->issue(
            new RewardIssue(
                event_id: $event->id,
                reward_rule_id: $ruleOne->id,
                reward_type: 'points',
                reward_value: 100.00,
            )
        );

        $repository->issue(
            new RewardIssue(
                event_id: $event->id,
                reward_rule_id: $ruleTwo->id,
                reward_type: 'voucher',
                reward_value: 20,
            )
        );

        $this->assertDatabaseCount('reward_issues', 2);
    });

    it('prevents duplicate reward issuance for same event and rule.', function () {

        $event = EloquentEvent::factory()->create();
        $rule = EloquentRewardRule::factory()->create();

        $repository = new EloquentRewardIssueRepository;

        $issue = new RewardIssue(
            event_id: $event->id,
            reward_rule_id: $rule->id,
            reward_type: 'points',
            reward_value: 100,
        );

        $repository->issue($issue);

        expect(fn () => $repository->issue($issue))
            ->toThrow(QueryException::class);

        $this->assertDatabaseCount('reward_issues', 1);
    });
});
