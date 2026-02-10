<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Category;
use App\Models\Tenant\Client;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function index()
    {
        return view('tenant.import.index');
    }

    public function template($module)
    {
        $templates = [
            'categories' => [
                'filename' => 'plantilla_categorias.csv',
                'headers' => ['nombre'],
                'example' => ['Electrónica']
            ],
            'clients' => [
                'filename' => 'plantilla_clientes.csv',
                'headers' => ['nombre', 'nit_ci', 'telefono', 'email'],
                'example' => ['Juan Pérez', '12345678', '555-1234', 'juan@example.com']
            ],
            'suppliers' => [
                'filename' => 'plantilla_proveedores.csv',
                'headers' => ['nombre', 'empresa', 'telefono', 'telefono_secundario', 'email', 'direccion'],
                'example' => ['Carlos López', 'Distribuidora XYZ', '555-5678', '555-9012', 'carlos@xyz.com', 'Av. Principal 123']
            ],
            'products' => [
                'filename' => 'plantilla_productos.csv',
                'headers' => ['codigo', 'nombre', 'categoria_id', 'precio_compra', 'precio_venta', 'stock', 'stock_minimo', 'stock_maximo', 'fecha_entrada', 'imagen', 'descripcion'],
                'example' => ['PROD001', 'Laptop HP', '1', '500.00', '750.00', '10', '5', '50', date('Y-m-d'), '', 'Laptop empresarial']
            ],
            'purchases' => [
                'filename' => 'plantilla_compras.csv',
                'headers' => ['codigo_producto', 'nombre_empresa_proveedor', 'cantidad', 'precio_unitario', 'numero_compra', 'comprobante', 'fecha_compra'],
                'example' => ['PROD001', 'Distribuidora XYZ', '10', '500.00', 'COMP-001', 'FAC-12345', date('Y-m-d')]
            ]
        ];

        if (!isset($templates[$module])) {
            abort(404);
        }

        $template = $templates[$module];
        
        $csv = fopen('php://temp', 'w');
        
        // UTF-8 BOM para Excel
        fprintf($csv, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($csv, $template['headers']);
        
        // Example row
        fputcsv($csv, $template['example']);
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $template['filename'] . '"');
    }

    public function importCategories(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file);
        
        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header if exists
            if ($index === 0 && strtolower($row[0] ?? '') === 'nombre') {
                continue;
            }

            $name = trim($row[0] ?? '');
            
            if (empty($name)) {
                $errors++;
                continue;
            }

            // Check duplicates
            if (Category::where('name', $name)->exists()) {
                $duplicates++;
                if ($skipDuplicates) continue;
            }

            try {
                Category::create(['name' => $name]);
                $created++;
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'created' => $created,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'error_messages' => $errorMessages
        ]);
    }

    public function importClients(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file);
        
        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            if ($index === 0 && (strtolower($row[0] ?? '') === 'nombre' || strtolower($row[0] ?? '') === 'name')) {
                continue;
            }

            $clientData = [
                'name' => trim($row[0] ?? ''),
                'nit_ci' => trim($row[1] ?? ''),
                'phone' => trim($row[2] ?? ''),
                'email' => trim($row[3] ?? '')
            ];

            // Validate
            $validator = Validator::make($clientData, [
                'name' => 'required|string|max:255',
                'nit_ci' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255'
            ]);

            if ($validator->fails()) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Check duplicates
            if (Client::where('nit_ci', $clientData['nit_ci'])->exists()) {
                $duplicates++;
                if ($skipDuplicates) continue;
            }

            try {
                Client::create($clientData);
                $created++;
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'created' => $created,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'error_messages' => $errorMessages
        ]);
    }

    public function importSuppliers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file);
        
        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            if ($index === 0 && (strtolower($row[0] ?? '') === 'nombre' || strtolower($row[0] ?? '') === 'name')) {
                continue;
            }

            $supplierData = [
                'name' => trim($row[0] ?? ''),
                'company' => trim($row[1] ?? ''),
                'phone' => trim($row[2] ?? ''),
                'secondary_phone' => trim($row[3] ?? '') ?: null,
                'email' => trim($row[4] ?? '') ?: null,
                'address' => trim($row[5] ?? '')
            ];

            // Validate
            $validator = Validator::make($supplierData, [
                'name' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'phone' => 'required|string|max:50',
                'secondary_phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:50',
                'address' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Check duplicates
            if (Supplier::where('phone', $supplierData['phone'])->exists()) {
                $duplicates++;
                if ($skipDuplicates) continue;
            }

            try {
                Supplier::create($supplierData);
                $created++;
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'created' => $created,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'error_messages' => $errorMessages
        ]);
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file);
        
        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            if ($index === 0 && (strtolower($row[0] ?? '') === 'codigo' || strtolower($row[0] ?? '') === 'code')) {
                continue;
            }

            $productData = [
                'code' => trim($row[0] ?? ''),
                'name' => trim($row[1] ?? ''),
                'category_id' => trim($row[2] ?? ''),
                'purchase_price' => trim($row[3] ?? ''),
                'sale_price' => trim($row[4] ?? ''),
                'stock' => trim($row[5] ?? ''),
                'min_stock' => trim($row[6] ?? ''),
                'max_stock' => trim($row[7] ?? ''),
                'entry_date' => trim($row[8] ?? date('Y-m-d')),
                'image' => trim($row[9] ?? '') ?: null,
                'description' => trim($row[10] ?? '') ?: null,
                'user_id' => auth()->id()
            ];

            // Validate
            $validator = Validator::make($productData, [
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'purchase_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'min_stock' => 'required|integer|min:0',
                'max_stock' => 'required|integer|min:0',
                'entry_date' => 'required|date',
                'image' => 'nullable|string|max:255',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Check duplicates
            if (Product::where('code', $productData['code'])->exists()) {
                $duplicates++;
                if ($skipDuplicates) continue;
            }

            try {
                Product::create($productData);
                $created++;
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'created' => $created,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'error_messages' => $errorMessages
        ]);
    }

    public function importPurchases(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file);
        
        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            if ($index === 0 && (strtolower($row[0] ?? '') === 'codigo_producto' || strtolower($row[0] ?? '') === 'product_code')) {
                continue;
            }

            $productCode = trim($row[0] ?? '');
            $supplierCompany = trim($row[1] ?? '');
            $quantity = trim($row[2] ?? '');
            $price = trim($row[3] ?? '');
            $nroCompra = trim($row[4] ?? '');
            $voucher = trim($row[5] ?? '');
            $purchaseDate = trim($row[6] ?? date('Y-m-d'));

            // Buscar IDs por claves únicas
            $product = Product::where('code', $productCode)->first();
            $supplier = Supplier::where('company', $supplierCompany)->first();

            if (!$product) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": Producto con código '{$productCode}' no encontrado.";
                continue;
            }

            if (!$supplier) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": Proveedor con empresa '{$supplierCompany}' no encontrado.";
                continue;
            }

            $purchaseData = [
                'product_id' => $product->id,
                'supplier_id' => $supplier->id,
                'quantity' => $quantity,
                'price' => $price,
                'nro_compra' => $nroCompra,
                'voucher' => $voucher,
                'purchase_date' => $purchaseDate,
                'user_id' => auth()->id()
            ];

            // Validar datos básicos
            $validator = Validator::make($purchaseData, [
                'product_id' => 'required|exists:products,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'nro_compra' => 'required|string|max:255',
                'voucher' => 'required|string|max:255',
                'purchase_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Validar Stock Máximo
            if (($product->stock + $quantity) > $product->max_stock) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": La cantidad excede el stock máximo permitido. Actual: {$product->stock}, Máx: {$product->max_stock}, Intento Agrear: {$quantity}.";
                continue;
            }

            // Validar Duplicado (Nro Compra)
            if (\App\Models\Tenant\Purchase::where('nro_compra', $nroCompra)->exists()) {
                $duplicates++;
                if ($skipDuplicates) continue;
            }

            try {
                // Crear Compra
                \App\Models\Tenant\Purchase::create($purchaseData);
                
                // Actualizar Stock del Producto
                $product->stock += $quantity;
                $product->save();

                $created++;
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Fila " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'created' => $created,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'error_messages' => $errorMessages
        ]);
    }

    private function parseFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();

        if ($extension === 'csv') {
            return $this->parseCsv($path);
        } else {
            // For Excel files, we'll use a simple approach
            // In production, you'd use PhpSpreadsheet
            return $this->parseCsv($path);
        }
    }

    private function parseCsv($path)
    {
        $data = [];
        
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }

        return $data;
    }
}
