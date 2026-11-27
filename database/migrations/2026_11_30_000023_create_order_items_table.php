<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id")->constrained("orders")->cascadeOnDelete();
            $table->foreignId("product_id")->nullable()->constrained("products")->nullOnDelete();
            $table->unsignedBigInteger("variant_id")->nullable(); // FK ditambah saat fase Pro
            $table->string("product_name");
            $table->string("variant_name")->nullable();
            $table->unsignedInteger("unit_price");
            $table->unsignedInteger("qty");
            $table->unsignedInteger("line_total");
            $table->unsignedInteger("weight_gram")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
