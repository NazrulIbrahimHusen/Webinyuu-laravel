<?php

namespace App\Models;

use App\Enums\PlanCode;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    /** Daftar flag fitur yang dikenal sistem (dipakai form admin dan hasFeature). */
    public const FEATURES = [
        'checkout' => 'Keranjang & checkout',
        'orders' => 'Kelola pesanan',
        'shipping' => 'Pengiriman & resi',
        'bg_color' => 'Warna latar katalog',
        'landing_page' => 'Landing page',
        'basic_report' => 'Laporan dasar',
        'variants' => 'Varian produk (Pro)',
        'advanced_stock' => 'Stok lanjutan (Pro)',
        'discounts' => 'Diskon (Pro)',
        'analytics' => 'Analitik (Pro)',
        'export' => 'Ekspor data (Pro)',
        'advanced_theme' => 'Tema lanjutan (Pro)',
    ];

    protected $guarded = [];

    protected $casts = ['features' => 'array', 'code' => PlanCode::class];

    public function hasFeature(string $feature): bool
    {
        return (bool) ($this->features[$feature] ?? false);
    }

    public function priceFor(\App\Enums\SubscriptionPeriod $period): ?int
    {
        return $period === \App\Enums\SubscriptionPeriod::Yearly ? $this->price_yearly : $this->price_monthly;
    }

    public static function basic(): self
    {
        return static::where('code', PlanCode::Basic->value)->firstOrFail();
    }
}
