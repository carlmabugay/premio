<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RewardLedgerEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'merchant_id',
        'customer_id',
        'type',
        'reference_type',
        'reference_id',
        'points',
        'created_at',
    ];
}
