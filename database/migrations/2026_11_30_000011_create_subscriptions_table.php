<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->foreignId("plan_id")->constrained("plans")->cascadeOnDelete();
            $table->string("period")->default("monthly");
            $table->string("status")->default("active");
            $table->timestamp("starts_at")->nullable();
            $table->timestamp("ends_at")->nullable();
            $table->timestamp("grace_ends_at")->nullable();
            $table->unsignedTinyInteger("last_reminder_day")->nullable();
            $table->foreignId("activated_by")->nullable()->constrained("users")->nullOnDelete();
            $table->index(["status","ends_at"]);
            $table->index(["user_id","status"]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
