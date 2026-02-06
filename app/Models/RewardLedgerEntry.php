<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardLedgerEntry extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'reward_rule_id',
        'subject_type',
        'subject_id',
        'reason',
        'points',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(RewardRule::class);
    }
}
