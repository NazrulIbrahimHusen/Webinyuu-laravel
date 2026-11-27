<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId("landing_page_id")->constrained("landing_pages")->cascadeOnDelete();
            $table->string("name");
            $table->text("content");
            $table->string("photo_path")->nullable();
            $table->unsignedInteger("sort_order")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_testimonials');
    }
};
