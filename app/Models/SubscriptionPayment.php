<?php

namespace App\Models;

use App\Enums\SubscriptionPaymentStatus;
use App\Enums\SubscriptionPeriod;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => SubscriptionPaymentStatus::class,
        'period' => SubscriptionPeriod::class,
        'due_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }

    public function isOpen(): bool
    {
        return in_array($this->status, [SubscriptionPaymentStatus::Pending, SubscriptionPaymentStatus::WaitingVerification], true);
    }

    public function canUploadProof(): bool
    {
        return $this->status === SubscriptionPaymentStatus::Rejected
            || ($this->status === SubscriptionPaymentStatus::Pending && $this->due_at?->isFuture());
    }
}
