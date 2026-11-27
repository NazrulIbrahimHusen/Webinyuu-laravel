<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id")->constrained("orders")->cascadeOnDelete();
            $table->foreignId("payment_method_id")->nullable()->constrained("payment_methods")->nullOnDelete();
            $table->string("method_label")->nullable();
            $table->unsignedInteger("amount");
            $table->string("proof_path")->nullable();
            $table->string("status")->default("waiting_verification");
            $table->text("reject_reason")->nullable();
            $table->foreignId("verified_by")->nullable()->constrained("users")->nullOnDelete();
            $table->timestamp("verified_at")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
