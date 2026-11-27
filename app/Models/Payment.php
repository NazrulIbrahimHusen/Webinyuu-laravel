<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];
    protected $casts = ['status' => PaymentStatus::class, 'verified_at' => 'datetime'];

    public function order() { return $this->belongsTo(Order::class); }
}
