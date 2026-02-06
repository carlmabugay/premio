<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'reward_type',
        'reward_amount',
        'is_active',
    ];
}
