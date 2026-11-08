<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // create the table
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // links to products
            $table->foreignId('user_id')->constrained(); // who recorded it
            $table->enum('type', ['stock_in', 'stock_out', 'adjustment']);
            $table->integer('quantity'); // + adds stock, - removes stock
            $table->string('reference_number', 50)->nullable(); // PO / SO number
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']); // speeds up history queries
        });
    }

    // undo: drop the table
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};