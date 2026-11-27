<?php

namespace App\Models;

use App\Enums\SubscriptionPeriod;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => SubscriptionStatus::class,
        'period' => SubscriptionPeriod::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'grace_ends_at' => 'datetime',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function user() { return $this->belongsTo(User::class); }

    /** Langganan yang sedang berlaku: active atau grace. */
    public function scopeCurrent(Builder $q): Builder
    {
        return $q->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Grace->value]);
    }
}
