<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_featured_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("landing_page_id")->constrained("landing_pages")->cascadeOnDelete();
            $table->foreignId("product_id")->constrained("products")->cascadeOnDelete();
            $table->unsignedInteger("sort_order")->default(0);
            $table->unique(["landing_page_id","product_id"]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_featured_products');
    }
};
