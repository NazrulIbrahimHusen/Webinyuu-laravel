<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("store_id")->constrained("stores")->cascadeOnDelete();
            $table->string("invoice_number")->unique();
            $table->string("access_token")->unique();
            $table->string("customer_name");
            $table->string("customer_phone");
            $table->string("customer_email")->nullable();
            $table->string("fulfillment")->default("delivery");
            $table->text("shipping_address")->nullable();
            $table->string("shipping_area")->nullable();
            $table->text("customer_note")->nullable();
            $table->unsignedInteger("subtotal");
            $table->unsignedInteger("discount_total")->default(0);
            $table->unsignedInteger("shipping_cost")->default(0);
            $table->unsignedInteger("total");
            $table->string("order_status")->default("new");
            $table->string("payment_status")->default("waiting_payment");
            $table->timestamp("completed_at")->nullable();
            $table->index(["store_id","order_status"]);
            $table->index(["store_id","created_at"]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
