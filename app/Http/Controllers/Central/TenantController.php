<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Tenant::with('domains');

            // Grid.js envía los parámetros por defecto
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $tenants = $query->orderBy('id', 'desc')
                             ->offset($offset)
                             ->limit($limit)
                             ->get();

            return response()->json([
                'data' => $tenants,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('central.tenants.index'),
                'edit' => route('central.tenants.edit', ':id'),
                'destroy' => route('central.tenants.destroy', ':id'),
                'markPaid' => route('central.tenants.mark-as-paid', ':id')
            ],
            'db_prefix' => config('database.connections.central.database') . '_',
            'tokens' => [
                'csrf' => csrf_token()
            ]
        ];

        return view('central.tenants.index', compact('config'));
    }

    public function checkId(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return response()->json(['available' => false]);
        
        // El dominio que se generaría
        $baseDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? $request->getHost();
        $domain = $id . '.' . $baseDomain;

        $tenantExists = Tenant::where('id', $id)->exists();
        $domainExists = \Stancl\Tenancy\Database\Models\Domain::where('domain', $domain)->exists();

        $available = !$tenantExists && !$domainExists;

        return response()->json([
            'available' => $available,
            'message' => !$available ? 'Este ID o Dominio ya está en uso' : 'ID disponible'
        ]);
    }

    public function create()
    {
        return view('central.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => [
                'required', 
                'string', 
                'max:60', 
                'unique:tenants,id', 
                'alpha_dash',
                function ($attribute, $value, $fail) use ($request) {
                    // Validar dominios reservados
                    if (in_array($value, ['admin', 'central', 'www', 'mail', 'api'])) {
                        $fail('Este ID está reservado por el sistema.');
                    }
                    
                    // Validar que el dominio no exista
                    $baseDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? $request->getHost();
                    $domain = $value . '.' . $baseDomain;
                    if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $domain)->exists()) {
                        $fail('El dominio ' . $domain . ' ya está siendo utilizado por otra empresa.');
                    }
                },
            ],
            'business_name' => 'nullable|string|max:255',
            'legal_name'    => 'nullable|string|max:255',
            'tax_id'        => 'nullable|string|max:50',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'website'       => 'nullable|url|max:255',
            'address'       => 'nullable|string',
            'currency'      => 'nullable|string|max:10',
            'business_type' => 'nullable|string|max:50',
            'timezone'      => 'nullable|string|max:50',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'invoice_footer' => 'nullable|string',
            'service_type'  => 'required|in:subscription,purchase',
            'subscription_period' => 'required_if:service_type,subscription|nullable|in:30,90,365',
            'next_payment_date' => 'required|date',
            'is_paid'       => 'nullable|boolean',
        ], [
            'id.unique' => 'Este nombre de empresa ya está registrado.',
            'id.alpha_dash' => 'El ID solo puede contener letras, números y guiones.',
            'logo.max' => 'El logo no debe pesar más de 2MB.',
        ]);

        try {
            $centralDbName = config('database.connections.central.database');
            $tenantId = $request->id;
            $tenantDbName = $centralDbName . '_' . $tenantId;

            // Procesar Logo
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('tenants/logos', 'public');
            }

            // Recopilar datos adicionales para el campo JSON 'data'
            $tenantData = [
                'business_name'  => $request->business_name,
                'legal_name'     => $request->legal_name,
                'tax_id'         => $request->tax_id,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'website'        => $request->website,
                'address'        => $request->address,
                'currency'       => $request->currency ?? 'COP',
                'business_type'  => $request->business_type,
                'timezone'       => $request->timezone ?? 'America/Bogota',
                'logo'           => $logoPath,
                'invoice_footer' => $request->invoice_footer,
                'service_type'   => $request->service_type,
                'subscription_period' => $request->subscription_period,
                'next_payment_date' => $request->next_payment_date,
                'is_paid'        => $request->boolean('is_paid', true),
            ];

            // Primero buscamos si ya existe para evitar duplicados
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                // Si no existe, lo creamos con sus datos iniciales
                $tenant = Tenant::make(['id' => $tenantId]);
                // Combinamos los datos base de Stancl con nuestros datos personalizados
                foreach ($tenantData as $key => $value) {
                    $tenant->$key = $value;
                }
                
                // Establecemos el nombre de la DB ANTES de guardar
                $tenant->setInternal('db_name', $tenantDbName);
                $tenant->save();
            } else {
                // Si ya existe (caso raro por la validación unique), actualizamos
                foreach ($tenantData as $key => $value) {
                    $tenant->$key = $value;
                }
                $tenant->setInternal('db_name', $tenantDbName);
                $tenant->save();
            }

            $output = "Empresa base creada correctamente.\n";

            // Si se marcó crear DB, procedemos con los pasos internos
            if ($request->boolean('create_db')) {
                // Asociamos el dominio
                $baseDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? $request->getHost();
                $tenant->domains()->firstOrCreate([
                    'domain' => $tenantId . '.' . $baseDomain
                ]);
                $output .= "Dominio {$tenantId}.{$baseDomain} configurado.\n";

                // Ejecutamos migraciones
                // NOTA: Stancl/Tenancy ya ejecuta la creación de la DB automáticamente al crear el Tenant (evento TenantCreated).
                // Sin embargo, hemos deshabilitado MigrateDatabase en el ServiceProvider para tener control manual aquí
                // y poder especificar el path correcto de las migraciones del tenant.
                
                $tenant->run(function () use (&$output) {
                    $output .= "Iniciando migraciones...\n";
                    Artisan::call('migrate', [
                        '--path' => 'database/migrations/tenant',
                        '--force' => true,
                    ]);
                    $output .= Artisan::output();
                });

                // Si el usuario marcó la opción de Seeders, los ejecutamos ahora
                if ($request->boolean('seed')) {
                    $tenant->run(function () use (&$output) {
                        $output .= "\nIniciando seeders...\n";
                        Artisan::call('db:seed', [
                            '--force' => true,
                        ]);
                        $output .= Artisan::output();
                    });
                }
            } else {
                $output .= "AVISO: Se omitió la creación de base de datos y tablas por solicitud del usuario.\n";
                $output .= "La empresa ha sido registrada como un registro técnico únicamente.\n";
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Empresa '{$tenantId}' registrada" . ($request->boolean('create_db') ? " con base de datos." : " (solo registro)."),
                    'output' => $output,
                    'tenant_id' => $tenantId
                ]);
            }

            return redirect()->route('central.tenants.index')
                ->with('success', "Empresa '{$tenantId}' registrada correctamente con la base de datos '{$tenantDbName}'" . ($request->boolean('seed') ? " y datos iniciales cargados." : "."));

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error en el proceso: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'Error en el proceso: ' . $e->getMessage());
        }
    }

    public function maintenance(Request $request, Tenant $tenant)
    {
        try {
            $type = $request->input('type', 'both'); // migrate, seed, both
            $output = "Iniciando proceso de mantenimiento para el inquilino: {$tenant->id}\n";
            $output .= "Modo: " . ($type === 'migrate' ? 'Solo Migraciones' : ($type === 'seed' ? 'Solo Seeders' : 'Completo')) . "\n";
            $output .= "Verificando conexión con el dominio...\n";

            // Aseguramos que tenga dominio si no lo tenía
            if ($tenant->domains()->count() === 0) {
                $baseDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? $request->getHost();
                $tenant->domains()->create([
                    'domain' => $tenant->id . '.' . $baseDomain
                ]);
                $output .= "Dominio {$tenant->id}.{$baseDomain} configurado.\n";
            }

            // Ejecutamos migraciones y/o seeders
            $tenant->run(function () use (&$output, $type) {
                if ($type === 'migrate' || $type === 'both') {
                    $output .= "\n--- EJECUTANDO MIGRACIONES ---\n";
                    Artisan::call('migrate', [
                        '--path' => 'database/migrations/tenant',
                        '--force' => true,
                    ]);
                    $output .= Artisan::output();
                }

                if ($type === 'seed' || $type === 'both') {
                    $output .= "\n--- MIGRANDO Y EJECUTANDO SEEDERS ---\n";
                    // Aseguramos migraciones antes de seeders por seguridad
                    if ($type === 'seed') {
                        Artisan::call('migrate', [
                            '--path' => 'database/migrations/tenant',
                            '--force' => true,
                        ]);
                    }
                    Artisan::call('db:seed', [
                        '--force' => true,
                    ]);
                    $output .= Artisan::output();
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Proceso completado exitosamente',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error en el mantenimiento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsPaid(Tenant $tenant)
    {
        try {
            $tenant->is_paid = true;
            
            // Si es suscripción, extender la fecha de pago desde hoy
            if ($tenant->service_type === 'subscription') {
                $days = (int) ($tenant->subscription_period ?? 30);
                $tenant->next_payment_date = now()->addDays($days)->format('Y-m-d');
            }
            
            $tenant->save();

            return response()->json([
                'status' => 'success',
                'message' => "La empresa '{$tenant->id}' ha sido marcada como pagada. Próximo pago: {$tenant->next_payment_date}",
                'next_payment_date' => $tenant->next_payment_date
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el estado de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    public function suspend(Tenant $tenant)
    {
        try {
            $tenant->is_paid = false;
            $tenant->save();

            return response()->json([
                'status' => 'success',
                'message' => "La empresa '{$tenant->id}' ha sido SUSPENDIDA correctamente."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al suspender la empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');
        return view('central.tenants.edit', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('central.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'business_name' => 'nullable|string|max:255',
            'legal_name'    => 'nullable|string|max:255',
            'tax_id'        => 'nullable|string|max:50',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'website'       => 'nullable|url|max:255',
            'address'       => 'nullable|string',
            'currency'      => 'nullable|string|max:10',
            'business_type' => 'nullable|string|max:50',
            'timezone'      => 'nullable|string|max:50',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'invoice_footer' => 'nullable|string',
            'service_type'  => 'required|in:subscription,purchase',
            'subscription_period' => 'required_if:service_type,subscription|nullable|in:30,90,365',
            'next_payment_date' => 'required|date',
            'is_paid'       => 'nullable|boolean',
        ]);

        try {
            // Recopilar datos para actualizar
            $tenantData = [
                'business_name'  => $request->business_name,
                'legal_name'     => $request->legal_name,
                'tax_id'         => $request->tax_id,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'website'        => $request->website,
                'address'        => $request->address,
                'currency'       => $request->currency,
                'business_type'  => $request->business_type,
                'timezone'       => $request->timezone,
                'invoice_footer' => $request->invoice_footer,
                'service_type'   => $request->service_type,
                'subscription_period' => $request->subscription_period,
                'next_payment_date' => $request->next_payment_date,
                'is_paid'        => $request->boolean('is_paid', false),
            ];

            // Procesar Logo solo si se subió uno nuevo
            if ($request->hasFile('logo')) {
                // Podríamos eliminar el logo anterior aquí si quisiéramos
                $tenantData['logo'] = $request->file('logo')->store('tenants/logos', 'public');
            }

            // Actualizar el tenant
            foreach ($tenantData as $key => $value) {
                $tenant->$key = $value;
            }
            $tenant->save();

            return redirect()->route('central.tenants.index')
                ->with('success', "Información de la empresa {$tenant->id} actualizada correctamente.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            $tenant->delete();
            return redirect()->route('central.tenants.index')
                ->with('success', 'Inquilino eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el inquilino: ' . $e->getMessage());
        }
    }
}
