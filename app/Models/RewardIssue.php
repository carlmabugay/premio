<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardIssue extends Model
{
    protected $fillable = [
        'event_id',
        'reward_rule_id',
        'reward_type',
        'reward_value',
    ];
}
