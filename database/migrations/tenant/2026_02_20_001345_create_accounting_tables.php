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
        // 1. Catálogo de Cuentas
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ej: 1.1.01
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->boolean('is_movement')->default(false); // true = acepta asientos
            $table->decimal('current_balance', 15, 2)->default(0); 
            $table->timestamps();
        });

        // 2. Cabecera de Asientos Contables
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description');
            $table->nullableMorphs('reference'); // reference_type, reference_id (Sale, Purchase, Expense)
            $table->string('reference_number')->nullable(); // Nro Factura, etc.
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'posted', 'void'])->default('posted');
            $table->timestamps();
        });

        // 3. Detalle de Asientos
        Schema::create('journal_entry_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_details');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
    }
};
