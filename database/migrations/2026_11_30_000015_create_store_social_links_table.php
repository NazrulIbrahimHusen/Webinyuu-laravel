<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId("store_id")->constrained("stores")->cascadeOnDelete();
            $table->string("platform");
            $table->string("url");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_social_links');
    }
};
