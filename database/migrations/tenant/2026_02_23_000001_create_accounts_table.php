<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->boolean('is_movement')->default(false);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->timestamps();
        });

        // Insertar cuentas básicas necesarias para el ExpenseObserver
        // 5.2.03 Servicios Básicos
        // 1.1.01.01 Caja General
        
        // Estructura básica mínima para que funcione
        DB::table('accounts')->insert([
            ['code' => '1', 'name' => 'ACTIVO', 'type' => 'asset', 'level' => 1, 'is_movement' => false, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5', 'name' => 'GASTOS', 'type' => 'expense', 'level' => 1, 'is_movement' => false, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        $activoId = DB::table('accounts')->where('code', '1')->value('id');
        $gastosId = DB::table('accounts')->where('code', '5')->value('id');

        DB::table('accounts')->insert([
            ['code' => '1.1', 'name' => 'ACTIVO CORRIENTE', 'type' => 'asset', 'level' => 2, 'is_movement' => false, 'parent_id' => $activoId, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5.2', 'name' => 'GASTOS OPERATIVOS', 'type' => 'expense', 'level' => 2, 'is_movement' => false, 'parent_id' => $gastosId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $activoCorrienteId = DB::table('accounts')->where('code', '1.1')->value('id');
        $gastosOperativosId = DB::table('accounts')->where('code', '5.2')->value('id');

        DB::table('accounts')->insert([
            ['code' => '1.1.01', 'name' => 'DISPONIBLE', 'type' => 'asset', 'level' => 3, 'is_movement' => false, 'parent_id' => $activoCorrienteId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $disponibleId = DB::table('accounts')->where('code', '1.1.01')->value('id');

        DB::table('accounts')->insert([
            ['code' => '1.1.01.01', 'name' => 'Caja General', 'type' => 'asset', 'level' => 4, 'is_movement' => true, 'parent_id' => $disponibleId, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '5.2.03', 'name' => 'Servicios Básicos', 'type' => 'expense', 'level' => 3, 'is_movement' => true, 'parent_id' => $gastosOperativosId, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};