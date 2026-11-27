<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("store_id")->unique()->constrained("stores")->cascadeOnDelete();
            $table->string("mode")->default("manual");
            $table->string("sender_name")->nullable();
            $table->string("sender_phone")->nullable();
            $table->text("origin_address")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("origin_location_id")->nullable();
            $table->json("couriers")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_shipping_settings');
    }
};
