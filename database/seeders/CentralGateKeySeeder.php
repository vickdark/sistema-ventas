<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CentralSetting; // Asegúrate de que este modelo exista y sea correcto

class CentralGateKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CentralSetting::updateOrCreate(
            ['key' => 'central_login_gate_key'],
            [
                'value' => 'miclave', // Valor por defecto, puedes cambiarlo
                'description' => 'Clave de acceso para el login central'
            ]
        );
    }
}
