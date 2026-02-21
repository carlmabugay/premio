<?php

namespace App\Http\Responses;

use App\Application\Results\RuleEvaluationResult;
use Illuminate\Http\JsonResponse;

readonly class EventIngestionResponse
{
    public function __construct(
        private array $request,
        private RuleEvaluationResult $result,
    ) {}

    public function make(): JsonResponse
    {
        if ($this->result->already_evaluated) {
            return response()->json([
                'status' => 'already_processed',
                'event' => [
                    'external_id' => $this->request['external_id'],
                    'type' => $this->request['type'],
                    'source' => $this->request['source'],
                ],
                'issued_rewards' => 0,
            ], 200);
        }

        return response()->json([
            'status' => 'processed',
            'event' => [
                'id' => $this->result->event_id,
                'external_id' => $this->request['external_id'],
                'type' => $this->request['type'],
                'source' => $this->request['source'],
            ],
            'rewards' => array_map(fn ($issue) => [
                'rule_id' => $issue->rewardRuleId(),
                'type' => $issue->rewardType(),
                'value' => $issue->rewardValue(),
            ], $this->result->issued_issues),
            'issued_rewards' => $this->result->issued_rewards,
        ], 201);
    }
}
