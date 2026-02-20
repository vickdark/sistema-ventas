<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cuentas existentes (opcional, para desarrollo)
        // Account::truncate();

        $accounts = [
            // 1. ACTIVOS
            [
                'code' => '1', 'name' => 'ACTIVOS', 'type' => 'asset', 'level' => 1, 'children' => [
                    ['code' => '1.1', 'name' => 'ACTIVO CORRIENTE', 'type' => 'asset', 'level' => 2, 'children' => [
                        ['code' => '1.1.01', 'name' => 'EFECTIVO Y EQUIVALENTES', 'type' => 'asset', 'level' => 3, 'children' => [
                            ['code' => '1.1.01.01', 'name' => 'Caja General', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                            ['code' => '1.1.01.02', 'name' => 'Caja Chica', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                            ['code' => '1.1.01.03', 'name' => 'Bancos', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                        ]],
                        ['code' => '1.1.02', 'name' => 'CUENTAS POR COBRAR', 'type' => 'asset', 'level' => 3, 'children' => [
                            ['code' => '1.1.02.01', 'name' => 'Clientes Nacionales', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                        ]],
                        ['code' => '1.1.03', 'name' => 'INVENTARIOS', 'type' => 'asset', 'level' => 3, 'children' => [
                            ['code' => '1.1.03.01', 'name' => 'Mercaderías', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                        ]],
                         ['code' => '1.1.04', 'name' => 'IMPUESTOS POR COBRAR', 'type' => 'asset', 'level' => 3, 'children' => [
                            ['code' => '1.1.04.01', 'name' => 'Crédito Fiscal IVA', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                        ]],
                    ]],
                    ['code' => '1.2', 'name' => 'ACTIVO NO CORRIENTE', 'type' => 'asset', 'level' => 2, 'children' => [
                        ['code' => '1.2.01', 'name' => 'PROPIEDAD PLANTA Y EQUIPO', 'type' => 'asset', 'level' => 3, 'children' => [
                            ['code' => '1.2.01.01', 'name' => 'Mobiliario y Equipo', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                            ['code' => '1.2.01.02', 'name' => 'Equipo de Cómputo', 'type' => 'asset', 'level' => 4, 'is_movement' => true],
                        ]],
                    ]],
                ]
            ],
            // 2. PASIVOS
            [
                'code' => '2', 'name' => 'PASIVOS', 'type' => 'liability', 'level' => 1, 'children' => [
                    ['code' => '2.1', 'name' => 'PASIVO CORRIENTE', 'type' => 'liability', 'level' => 2, 'children' => [
                        ['code' => '2.1.01', 'name' => 'CUENTAS POR PAGAR', 'type' => 'liability', 'level' => 3, 'children' => [
                            ['code' => '2.1.01.01', 'name' => 'Proveedores Nacionales', 'type' => 'liability', 'level' => 4, 'is_movement' => true],
                        ]],
                        ['code' => '2.1.02', 'name' => 'IMPUESTOS POR PAGAR', 'type' => 'liability', 'level' => 3, 'children' => [
                            ['code' => '2.1.02.01', 'name' => 'Débito Fiscal IVA', 'type' => 'liability', 'level' => 4, 'is_movement' => true],
                            ['code' => '2.1.02.02', 'name' => 'Impuesto Sobre la Renta', 'type' => 'liability', 'level' => 4, 'is_movement' => true],
                        ]],
                    ]],
                ]
            ],
            // 3. PATRIMONIO
            [
                'code' => '3', 'name' => 'PATRIMONIO', 'type' => 'equity', 'level' => 1, 'children' => [
                    ['code' => '3.1', 'name' => 'CAPITAL', 'type' => 'equity', 'level' => 2, 'children' => [
                        ['code' => '3.1.01', 'name' => 'Capital Social', 'type' => 'equity', 'level' => 3, 'is_movement' => true],
                    ]],
                    ['code' => '3.2', 'name' => 'RESULTADOS', 'type' => 'equity', 'level' => 2, 'children' => [
                        ['code' => '3.2.01', 'name' => 'Utilidad del Ejercicio', 'type' => 'equity', 'level' => 3, 'is_movement' => true],
                        ['code' => '3.2.02', 'name' => 'Pérdida del Ejercicio', 'type' => 'equity', 'level' => 3, 'is_movement' => true],
                    ]],
                ]
            ],
            // 4. INGRESOS
            [
                'code' => '4', 'name' => 'INGRESOS', 'type' => 'revenue', 'level' => 1, 'children' => [
                    ['code' => '4.1', 'name' => 'INGRESOS DE OPERACIÓN', 'type' => 'revenue', 'level' => 2, 'children' => [
                        ['code' => '4.1.01', 'name' => 'Ventas de Mercadería', 'type' => 'revenue', 'level' => 3, 'is_movement' => true],
                        ['code' => '4.1.02', 'name' => 'Servicios Prestados', 'type' => 'revenue', 'level' => 3, 'is_movement' => true],
                    ]],
                ]
            ],
            // 5. GASTOS
            [
                'code' => '5', 'name' => 'GASTOS', 'type' => 'expense', 'level' => 1, 'children' => [
                    ['code' => '5.1', 'name' => 'COSTO DE VENTAS', 'type' => 'expense', 'level' => 2, 'children' => [
                         ['code' => '5.1.01', 'name' => 'Costo de Ventas', 'type' => 'expense', 'level' => 3, 'is_movement' => true],
                    ]],
                    ['code' => '5.2', 'name' => 'GASTOS DE OPERACIÓN', 'type' => 'expense', 'level' => 2, 'children' => [
                        ['code' => '5.2.01', 'name' => 'Sueldos y Salarios', 'type' => 'expense', 'level' => 3, 'is_movement' => true],
                        ['code' => '5.2.02', 'name' => 'Alquileres', 'type' => 'expense', 'level' => 3, 'is_movement' => true],
                        ['code' => '5.2.03', 'name' => 'Servicios Básicos (Luz, Agua, Internet)', 'type' => 'expense', 'level' => 3, 'is_movement' => true],
                        ['code' => '5.2.04', 'name' => 'Papelería y Útiles', 'type' => 'expense', 'level' => 3, 'is_movement' => true],
                    ]],
                ]
            ],
        ];

        $this->createAccounts($accounts);
    }

    private function createAccounts(array $accounts, ?int $parentId = null)
    {
        foreach ($accounts as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $data['parent_id'] = $parentId;
            
            // Buscar o crear por código para evitar duplicados si se corre varias veces
            $account = Account::firstOrCreate(
                ['code' => $data['code']],
                $data
            );

            if (!empty($children)) {
               $this->createAccounts($children, $account->id);
            }
        }
    }
}
