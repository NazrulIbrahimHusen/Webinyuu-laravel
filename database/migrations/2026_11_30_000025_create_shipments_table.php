<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id")->unique()->constrained("orders")->cascadeOnDelete();
            $table->string("courier")->nullable();
            $table->string("service")->nullable();
            $table->string("tracking_number")->nullable();
            $table->timestamp("shipped_at")->nullable();
            $table->string("status")->default("not_shipped");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
