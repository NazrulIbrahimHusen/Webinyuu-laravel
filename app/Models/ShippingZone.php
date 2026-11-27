<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use BelongsToStore;

    protected $guarded = [];
}
