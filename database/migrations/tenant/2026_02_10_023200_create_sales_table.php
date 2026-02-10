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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('nro_venta');
            $table->unsignedBigInteger('client_id');
            $table->decimal('total_paid', 10, 2);
            $table->unsignedBigInteger('user_id');
            $table->date('sale_date');
            $table->string('voucher', 255)->nullable();
            $table->string('payment_type', 50)->default('CONTADO');
            $table->string('payment_status', 50)->default('PAGADO');
            $table->dateTime('credit_payment_date')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
