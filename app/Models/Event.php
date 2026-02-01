<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    public $timestamps = false;
    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function ($model) {
           $model->id = (string) Str::uuid();
        });
    }
}
