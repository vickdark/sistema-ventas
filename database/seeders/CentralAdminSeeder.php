<?php

namespace Database\Seeders;

use App\Models\Central\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CentralAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@multitenancy.test'],
            [
                'name' => 'Admin Central',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->call(CentralGateKeySeeder::class);
    }
}
