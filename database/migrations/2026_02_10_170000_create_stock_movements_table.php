<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock movements = audit trail for every stock change
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['initial', 'purchase', 'sale', 'damage', 'correction']);
            $table->decimal('kg', 10, 2); // positive = in, negative = out
            $table->decimal('balance_after', 10, 2); // snapshot of stock after this movement
            $table->string('reference_type')->nullable(); // e.g. 'App\Models\Sale'
            $table->unsignedBigInteger('reference_id')->nullable(); // e.g. sale ID
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // Add initial_stock_kg and avg_egg_weight to products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('initial_stock_kg', 10, 2)->default(0)->after('current_stock_kg');
            $table->decimal('avg_egg_weight', 6, 4)->default(0.0600)->after('lead_time_days');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['initial_stock_kg', 'avg_egg_weight']);
        });
    }
};
