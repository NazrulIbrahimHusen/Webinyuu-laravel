<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use BelongsToStore;

    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];
}
