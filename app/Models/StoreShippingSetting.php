<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreShippingSetting extends Model
{
    protected $guarded = [];
    protected $casts = ['couriers' => 'array'];
}
