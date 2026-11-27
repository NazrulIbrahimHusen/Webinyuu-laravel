<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Harga masih placeholder: tetapkan saat tahap penetapan harga.
        $plans = [
            ['code' => 'basic', 'name' => 'Basic', 'price_monthly' => 0, 'price_yearly' => null,
             'max_products' => 30, 'max_featured_products' => 0, 'features' => []],
            ['code' => 'starter', 'name' => 'Starter', 'price_monthly' => 29000, 'price_yearly' => null,
             'max_products' => 50, 'max_featured_products' => 3,
             'features' => ['checkout' => true, 'orders' => true, 'shipping' => true, 'bg_color' => true,
                            'landing_page' => true, 'basic_report' => true]],
            ['code' => 'pro', 'name' => 'Pro', 'price_monthly' => 79000, 'price_yearly' => null,
             'max_products' => null, 'max_featured_products' => 12,
             'features' => ['checkout' => true, 'orders' => true, 'shipping' => true, 'bg_color' => true,
                            'landing_page' => true, 'basic_report' => true, 'variants' => true,
                            'advanced_stock' => true, 'discounts' => true, 'analytics' => true,
                            'export' => true, 'advanced_theme' => true]],
        ];

        foreach ($plans as $p) {
            Plan::updateOrCreate(['code' => $p['code']], $p);
        }
    }
}
