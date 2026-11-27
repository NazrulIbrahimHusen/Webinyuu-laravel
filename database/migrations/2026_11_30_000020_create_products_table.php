<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("store_id")->constrained("stores")->cascadeOnDelete();
            $table->foreignId("category_id")->nullable()->constrained("categories")->nullOnDelete();
            $table->string("name");
            $table->string("slug");
            $table->text("description")->nullable();
            $table->unsignedInteger("price");
            $table->unsignedInteger("weight_gram")->default(0);
            $table->boolean("track_stock")->default(false);
            $table->unsignedInteger("stock")->nullable();
            $table->string("status")->default("draft");
            $table->softDeletes();
            $table->unique(["store_id","slug"]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
