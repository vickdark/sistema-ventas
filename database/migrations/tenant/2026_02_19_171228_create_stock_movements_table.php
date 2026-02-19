<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['input', 'output']);
            $table->integer('quantity');
            $table->string('reason'); // 'sale', 'purchase', 'adjustment', 'return', 'transfer', etc.
            $table->string('description')->nullable();
            $table->integer('prev_stock');
            $table->integer('new_stock');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable(); // Sale, Purchase, etc.
            $table->timestamps();
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
