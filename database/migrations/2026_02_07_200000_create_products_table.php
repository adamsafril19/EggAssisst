<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Telur');
            $table->decimal('current_stock_kg', 10, 2)->default(0);
            $table->decimal('current_price', 12, 2)->default(0);
            $table->decimal('alert_threshold_days', 5, 2)->default(2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
