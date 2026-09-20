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
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            $table->tenantColumn();
            $table->foreignId('product_id')->constrained('products')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('warehouse_id')->constrained('warehouses')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('quantity');
            $table->timestamp('updated_at')->nullable();

            $table->unique(['tenant_id', 'product_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
