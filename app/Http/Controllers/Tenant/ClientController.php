<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Client::query();

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nit_ci', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $clients = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $clients,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('clients.index'),
                'edit' => route('clients.edit', ':id'),
                'destroy' => route('clients.destroy', ':id')
            ]
        ];

        return view('tenant.clients.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Detect if it's a single client (POS) or bulk (Regular form)
        if ($request->has('name')) {
            $rules = [
                'name' => 'required|string|max:255',
                'nit_ci' => 'required|string|max:255|unique:clients,nit_ci',
                'phone' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
            ];
            
            if ($request->ajax() || $request->wantsJson()) {
                $request->validate($rules);
                $client = Client::create($request->all());
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cliente registrado correctamente.',
                    'data' => $client
                ]);
            }

            $request->validate($rules);
            Client::create($request->all());
            return redirect()->route('clients.index')->with('success', 'Cliente registrado correctamente.');
        }

        // Bulk creation logic
        $request->validate([
            'clients' => 'required|array|min:1|max:5',
            'clients.*.name' => 'required|string|max:255',
            'clients.*.nit_ci' => 'required|string|max:255|distinct',
            'clients.*.phone' => 'required|string|max:255',
            'clients.*.email' => 'required|email|max:255',
        ], [
            'clients.required' => 'Debe agregar al menos un cliente.',
            'clients.max' => 'Solo puede registrar hasta 5 clientes a la vez.',
            'clients.*.nit_ci.distinct' => 'No puede haber NITs/CIs duplicados.',
        ]);

        $created = 0;
        $duplicates = [];

        foreach ($request->clients as $clientData) {
            if (Client::where('nit_ci', $clientData['nit_ci'])->exists()) {
                $duplicates[] = $clientData['nit_ci'];
                continue;
            }

            Client::create($clientData);
            $created++;
        }

        $message = "Se registraron {$created} cliente(s) exitosamente.";
        if (count($duplicates) > 0) {
            $message .= " Los siguientes NITs/CIs ya existían: " . implode(', ', $duplicates);
        }

        return redirect()->route('clients.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('tenant.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('tenant.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nit_ci' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        $client->update($request->all());
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
