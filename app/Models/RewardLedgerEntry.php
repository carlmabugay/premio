<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardLedgerEntry extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'customer_id',
        'type',
        'reference_type',
        'reference_id',
        'points',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
