<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId("store_id")->unique()->constrained("stores")->cascadeOnDelete();
            $table->string("status")->default("draft");
            $table->string("meta_title")->nullable();
            $table->string("meta_description")->nullable();
            $table->json("theme")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
