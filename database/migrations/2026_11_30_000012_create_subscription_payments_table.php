<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->foreignId("plan_id")->constrained("plans")->cascadeOnDelete();
            $table->foreignId("subscription_id")->nullable()->constrained("subscriptions")->nullOnDelete();
            $table->string("invoice_number")->unique();
            $table->string("period");
            $table->unsignedInteger("base_amount");
            $table->unsignedSmallInteger("unique_code");
            $table->unsignedInteger("total_amount");
            $table->string("method")->default("manual");
            $table->string("proof_path")->nullable();
            $table->string("gateway_reference")->nullable();
            $table->string("status")->default("pending");
            $table->timestamp("due_at")->nullable();
            $table->text("reject_reason")->nullable();
            $table->foreignId("verified_by")->nullable()->constrained("users")->nullOnDelete();
            $table->timestamp("verified_at")->nullable();
            $table->index(["status","due_at"]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
