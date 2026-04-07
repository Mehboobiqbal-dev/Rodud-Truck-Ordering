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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pickup_location', 500);
            $table->string('delivery_location', 500);
            $table->string('cargo_size', 100);
            $table->decimal('cargo_weight', 10, 2);
            $table->text('notes')->nullable();
            $table->datetime('pickup_datetime');
            $table->datetime('delivery_datetime');
            $table->enum('status', ['pending', 'in_progress', 'delivered'])->default('pending');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
