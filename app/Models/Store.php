<?php

namespace App\Models;

use App\Enums\BusinessType;
use App\Services\PlanService;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $guarded = [];

    protected $casts = [
        'theme' => 'array',
        'is_active' => 'boolean',
        'business_type' => BusinessType::class,
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function products() { return $this->hasMany(Product::class); }
    public function categories() { return $this->hasMany(Category::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function paymentMethods() { return $this->hasMany(PaymentMethod::class)->orderBy('sort_order'); }
    public function shippingZones() { return $this->hasMany(ShippingZone::class)->orderBy('sort_order'); }
    public function shippingSetting() { return $this->hasOne(StoreShippingSetting::class); }

    private ?Plan $effectivePlanCache = null;

    /** Satu-satunya pintu untuk mengetahui paket yang berlaku. */
    public function effectivePlan(): Plan
    {
        return $this->effectivePlanCache ??= app(PlanService::class)->effectivePlan($this);
    }

    public function hasFeature(string $feature): bool
    {
        return $this->effectivePlan()->hasFeature($feature);
    }

    public function getRouteKeyName(): string { return 'slug'; }
}
