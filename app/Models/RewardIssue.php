<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardIssue extends Model
{
    protected $fillable = [
        'event_id',
        'reward_rule_id',
        'reward_type',
        'reward_value',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
