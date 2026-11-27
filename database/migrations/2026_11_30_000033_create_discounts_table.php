<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("store_id")->constrained("stores")->cascadeOnDelete();
            $table->string("name");
            $table->string("type"); // percent|fixed
            $table->unsignedInteger("value");
            $table->string("applies_to")->default("all");
            $table->timestamp("starts_at")->nullable();
            $table->timestamp("ends_at")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
