<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $guarded = [];
    protected $casts = ['status' => ShipmentStatus::class, 'shipped_at' => 'datetime'];
}
