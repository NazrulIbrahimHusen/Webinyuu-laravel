<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId("store_id")->constrained("stores")->cascadeOnDelete();
            $table->string("type"); // bank|ewallet|qris
            $table->string("label");
            $table->string("account_name")->nullable();
            $table->string("account_number")->nullable();
            $table->string("qris_image_path")->nullable();
            $table->text("instructions")->nullable();
            $table->boolean("is_active")->default(true);
            $table->unsignedInteger("sort_order")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
