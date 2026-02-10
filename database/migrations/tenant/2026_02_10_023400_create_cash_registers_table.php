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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->dateTime('opening_date');
            $table->time('scheduled_closing_time')->nullable();
            $table->dateTime('closing_date')->nullable();
            $table->decimal('initial_amount', 10, 2);
            $table->decimal('final_amount', 10, 2)->nullable();
            $table->integer('sales_count')->nullable();
            $table->decimal('total_sales', 10, 2)->nullable();
            $table->text('observations')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('status', 255);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
