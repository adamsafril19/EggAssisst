<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correction_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('old_value', 10, 2);
            $table->decimal('new_value', 10, 2);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correction_logs');
    }
};
