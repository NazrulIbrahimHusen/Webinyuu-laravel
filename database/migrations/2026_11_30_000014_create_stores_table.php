<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->unique()->constrained()->cascadeOnDelete();
            $table->string("slug")->unique();
            $table->string("name");
            $table->string("business_type")->default("physical"); // fnb|physical|digital|service
            $table->string("logo_path")->nullable();
            $table->text("description")->nullable();
            $table->text("address")->nullable();
            $table->string("whatsapp")->nullable();
            $table->string("email")->nullable();
            $table->string("bg_color")->nullable();
            $table->json("theme")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
