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
        // 1. Agregar campos a purchases para control de deuda
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('voucher');
            }
            if (!Schema::hasColumn('purchases', 'pending_amount')) {
                $table->decimal('pending_amount', 10, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('purchases', 'payment_status')) {
                $table->string('payment_status', 50)->default('PAGADO')->after('pending_amount'); // PAGADO, PENDIENTE, PARCIAL
            }
            if (!Schema::hasColumn('purchases', 'due_date')) {
                $table->date('due_date')->nullable()->after('payment_status');
            }
        });

        // 2. Crear tabla de abonos/pagos a proveedores
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date');
            $table->string('payment_method')->default('EFECTIVO'); // EFECTIVO, TRANSFERENCIA, CHEQUE
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
        
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'pending_amount', 'payment_status', 'due_date']);
        });
    }
};
