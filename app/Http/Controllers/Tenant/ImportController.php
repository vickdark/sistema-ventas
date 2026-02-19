<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Category;
use App\Models\Tenant\Client;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Traits\Tenant\HasImportParsers;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    use HasImportParsers;

    public function index()
    {
        return view('tenant.import.index');
    }

    public function template(Request $request, $module)
    {
        $format = $request->query('format', 'csv');
        $templates = [
            'categories' => [
                'filename' => 'plantilla_categorias',
                'headers' => ['nombre'],
                'example' => ['Electrónica']
            ],
            'clients' => [
                'filename' => 'plantilla_clientes',
                'headers' => ['nombre', 'nit_ci', 'telefono', 'email'],
                'example' => ['Juan Pérez', '12345678', '555-1234', 'juan@example.com']
            ],
            'suppliers' => [
                'filename' => 'plantilla_proveedores',
                'headers' => ['nombre', 'empresa', 'telefono', 'telefono_secundario', 'email', 'direccion'],
                'example' => ['Carlos López', 'Distribuidora XYZ', '555-5678', '555-9012', 'carlos@xyz.com', 'Av. Principal 123']
            ],
            'products' => [
                'filename' => 'plantilla_productos',
                'headers' => ['codigo', 'nombre', 'categoria', 'proveedor', 'precio_compra', 'precio_venta', 'stock', 'stock_minimo', 'stock_maximo', 'fecha_entrada', 'imagen', 'descripcion'],
                'example' => ['PROD001', 'Laptop HP', 'Electrónica', 'Distribuidora XYZ', '500.00', '750.00', '10', '5', '50', date('Y-m-d'), '', 'Laptop empresarial']
            ],
            'purchases' => [
                'filename' => 'plantilla_compras',
                'headers' => ['codigo_producto', 'nombre_empresa_proveedor', 'cantidad', 'precio_unitario', 'numero_compra', 'comprobante', 'fecha_compra'],
                'example' => ['PROD001', 'Distribuidora XYZ', '10', '500.00', 'COMP-001', 'FAC-12345', date('Y-m-d')]
            ]
        ];

        if (!isset($templates[$module])) {
            abort(404);
        }

        $template = $templates[$module];
        $filename = $template['filename'] . '.' . ($format === 'excel' ? 'xlsx' : 'csv');

        if ($format === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Headers
            $sheet->fromArray([$template['headers']], NULL, 'A1');
            // Example row
            $sheet->fromArray([$template['example']], NULL, 'A2');

            $writer = new Xlsx($spreadsheet);
            
            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // CSV logic (default)
        $csv = fopen('php://temp', 'w');
        fprintf($csv, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
        fputcsv($csv, $template['headers']);
        fputcsv($csv, $template['example']);
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function importCategories(Request $request)
    {
        ini_set('memory_limit', '512M');
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file->getRealPath(), $extension);
        
        if (count($data) > 50) {
            $path = $file->store('temp_imports');
            \App\Jobs\Tenant\ImportCategoriesJob::dispatch($path, $extension, $skipDuplicates);
            return redirect()->back()->with('info', 'El archivo es grande (' . count($data) . ' filas) y se está procesando en segundo plano.');
        }

        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header if exists
            $firstCell = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0] ?? ''));
            if ($index === 0 && strtolower($firstCell) === 'nombre') {
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
        ini_set('memory_limit', '512M');
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file->getRealPath(), $extension);
        
        if (count($data) > 50) {
            $path = $file->store('temp_imports');
            \App\Jobs\Tenant\ImportClientsJob::dispatch($path, $extension, $skipDuplicates);
            return redirect()->back()->with('info', 'El archivo es grande (' . count($data) . ' filas) y se está procesando en segundo plano.');
        }

        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            $firstCell = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0] ?? ''));
            if ($index === 0 && (strtolower($firstCell) === 'nombre' || strtolower($firstCell) === 'name')) {
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
        ini_set('memory_limit', '512M');
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file->getRealPath(), $extension);
        
        if (count($data) > 50) {
            $path = $file->store('temp_imports');
            \App\Jobs\Tenant\ImportSuppliersJob::dispatch($path, $extension, $skipDuplicates);
            return redirect()->back()->with('info', 'El archivo es grande (' . count($data) . ' filas) y se está procesando en segundo plano.');
        }

        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            $firstCell = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0] ?? ''));
            if ($index === 0 && (strtolower($firstCell) === 'nombre' || strtolower($firstCell) === 'name')) {
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
        // Aumentar memoria para procesar archivos grandes
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file->getRealPath(), $extension);
        
        // Si el archivo tiene más de 50 filas, procesar en segundo plano
        if (count($data) > 50) {
            $path = $file->store('temp_imports');
            \App\Jobs\Tenant\ImportProductsJob::dispatch($path, $extension, Auth::id(), $skipDuplicates);
            
            return redirect()->back()->with('info', 'El archivo es grande (' . count($data) . ' filas) y se está procesando en segundo plano. Los productos aparecerán gradualmente.');
        }

        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            $firstCell = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0] ?? ''));
            if ($index === 0 && (strtolower($firstCell) === 'codigo' || strtolower($firstCell) === 'code')) {
                continue;
            }

            $categoryName = trim($row[2] ?? '');
            $supplierName = trim($row[3] ?? '');
            
            // Find or create category
            $categoryId = null;
            if (!empty($categoryName)) {
                $category = Category::firstOrCreate(['name' => $categoryName]);
                $categoryId = $category->id;
            }

            // Find supplier by name or company
            $supplierId = null;
            if (!empty($supplierName)) {
                $supplier = Supplier::where('name', 'LIKE', $supplierName)
                    ->orWhere('company', 'LIKE', $supplierName)
                    ->first();
                if ($supplier) {
                    $supplierId = $supplier->id;
                } else {
                    // Si no existe, crear el proveedor básico con el nombre proporcionado
                    try {
                        $supplier = Supplier::create([
                            'name' => $supplierName,
                            'company' => $supplierName,
                            'phone' => 'Pendiente', // Requerido por la tabla
                            'address' => 'Pendiente' // Requerido por la tabla
                        ]);
                        $supplierId = $supplier->id;
                    } catch (\Exception $e) {
                        $errors++;
                        $errorMessages[] = "Fila " . ($index + 1) . ": No se pudo crear el proveedor '{$supplierName}'. " . $e->getMessage();
                        continue;
                    }
                }
            }

            $productData = [
                'code' => trim($row[0] ?? ''),
                'name' => trim($row[1] ?? ''),
                'category_id' => $categoryId,
                'purchase_price' => trim($row[4] ?? ''),
                'sale_price' => trim($row[5] ?? ''),
                'stock' => trim($row[6] ?? ''),
                'min_stock' => trim($row[7] ?? ''),
                'max_stock' => trim($row[8] ?? ''),
                'entry_date' => $this->transformDate($row[9] ?? null),
                'image' => trim($row[10] ?? '') ?: null,
                'description' => trim($row[11] ?? '') ?: null,
                'user_id' => Auth::id()
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
                // Manejar Imagen si existe
                $imagePath = null;
                $imageUrl = trim($row[10] ?? '');
                
                if (!empty($imageUrl)) {
                    $imagePath = $this->downloadAndStoreImage($imageUrl, $productData['code']);
                    if ($imagePath) {
                        $productData['image'] = $imagePath;
                    }
                }

                $product = Product::create($productData);
                
                // Relacionar con el proveedor si existe
                if ($supplierId) {
                    $product->suppliers()->attach($supplierId);
                }

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
        ini_set('memory_limit', '512M');
        $request->validate([
            'file' => 'required|file|extensions:csv,xlsx,xls|max:10240'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $skipDuplicates = $request->boolean('skip_duplicates', true);

        $data = $this->parseFile($file->getRealPath(), $extension);
        
        if (count($data) > 50) {
            $path = $file->store('temp_imports');
            \App\Jobs\Tenant\ImportPurchasesJob::dispatch($path, $extension, Auth::id(), $skipDuplicates);
            return redirect()->back()->with('info', 'El archivo es grande (' . count($data) . ' filas) y se está procesando en segundo plano.');
        }

        $created = 0;
        $duplicates = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            // Skip header
            $firstCell = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0] ?? ''));
            if ($index === 0 && (strtolower($firstCell) === 'codigo_producto' || strtolower($firstCell) === 'product_code')) {
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
                'user_id' => Auth::id()
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
                
                // Actualizar Stock del Producto using helper
                $product->addStock($quantity, 'Importación de Compra', 'Carga masiva: Compra #' . $nroCompra);

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

    private function downloadAndStoreImage($url, $code)
    {
        try {
            // Si la URL no empieza con http, ignorar (o manejar como ruta local si fuera necesario)
            if (!Str::startsWith($url, ['http://', 'https://'])) {
                return null;
            }

            // Timeout de 5 segundos por imagen para no bloquear el proceso principal
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                $extension = 'jpg'; // default
                
                if (Str::contains($contentType, 'image/png')) $extension = 'png';
                elseif (Str::contains($contentType, 'image/jpeg')) $extension = 'jpg';
                elseif (Str::contains($contentType, 'image/webp')) $extension = 'webp';
                elseif (Str::contains($contentType, 'image/gif')) $extension = 'gif';

                $filename = 'products/' . $code . '_' . time() . '.' . $extension;
                
                Storage::disk('public')->put($filename, $response->body());
                
                return $filename;
            }
        } catch (\Exception $e) {
            // Silenciosamente fallar la descarga de imagen pero permitir que el producto se cree
            return null;
        }

        return null;
    }
}
