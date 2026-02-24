<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardRule extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'name',
        'event_type',
        'reward_type',
        'reward_value',
        'is_active',
        'starts_at',
        'ends_at',
        'conditions',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conditions' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
}
