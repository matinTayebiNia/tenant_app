<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->tenantColumn();
            $table->foreignId('product_id')->constrained('products')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('warehouse_id')->constrained('warehouses')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('type', ['in', 'out', 'transfer']);
            $table->unsignedBigInteger('quantity');
            $table->string('reference');
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
