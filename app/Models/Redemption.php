<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'customer_id',
        'type',
        'external_id',
        'points',
        'status',
        'meta_data',
        'processed_at',
        'created_at',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
