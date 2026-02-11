<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\Product;
use App\Models\Tenant\Category;
use App\Models\Tenant\Supplier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Traits\Tenant\HasImportParsers;
use Illuminate\Support\Facades\Log;

class ImportProductsJob implements ShouldQueue
{
    use Queueable, HasImportParsers;

    protected $filePath;
    protected $extension;
    protected $userId;
    protected $skipDuplicates;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, string $extension, int $userId, bool $skipDuplicates = true)
    {
        $this->filePath = $filePath;
        $this->extension = $extension;
        $this->userId = $userId;
        $this->skipDuplicates = $skipDuplicates;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Aumentar memoria para procesar archivos grandes y descarga de imágenes
        ini_set('memory_limit', '512M');
        set_time_limit(600); // 10 minutos para el job

        $fullPath = storage_path('app/' . $this->filePath);
        
        if (!file_exists($fullPath)) {
            Log::error("Archivo de importación no encontrado: " . $fullPath);
            return;
        }

        $data = $this->parseFile($fullPath, $this->extension);

        foreach ($data as $index => $row) {
            // Saltamos la cabecera si es necesario (ya procesada en el controlador usualmente, 
            // pero por seguridad lo validamos aquí si el array la incluye)
            $firstCell = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0] ?? ''));
            if ($index === 0 && (strtolower($firstCell) === 'codigo' || strtolower($firstCell) === 'code')) {
                continue;
            }

            $categoryName = trim($row[2] ?? '');
            $supplierName = trim($row[3] ?? '');
            
            // Buscar o crear categoría
            $categoryId = null;
            if (!empty($categoryName)) {
                $category = Category::firstOrCreate(['name' => $categoryName]);
                $categoryId = $category->id;
            }

            // Buscar proveedor
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
                            'phone' => 'Pendiente',
                            'address' => 'Pendiente'
                        ]);
                        $supplierId = $supplier->id;
                    } catch (\Exception $e) {
                        Log::error("Error creando proveedor '{$supplierName}' en Job: " . $e->getMessage());
                    }
                }
            }

            $productData = [
                'code' => trim($row[0] ?? ''),
                'name' => trim($row[1] ?? ''),
                'category_id' => $categoryId,
                'purchase_price' => trim($row[4] ?? 0),
                'sale_price' => trim($row[5] ?? 0),
                'stock' => trim($row[6] ?? 0),
                'min_stock' => trim($row[7] ?? 0),
                'max_stock' => trim($row[8] ?? 0),
                'entry_date' => $this->transformDate($row[9] ?? null),
                'description' => trim($row[11] ?? '') ?: null,
                'user_id' => $this->userId
            ];

            // Validar datos básicos
            $validator = Validator::make($productData, [
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($validator->fails()) {
                continue;
            }

            // Verificar duplicados
            if ($this->skipDuplicates && Product::where('code', $productData['code'])->exists()) {
                continue;
            }

            try {
                // Manejar Imagen si existe
                $imageUrl = trim($row[10] ?? '');
                if (!empty($imageUrl)) {
                    $imagePath = $this->downloadAndStoreImage($imageUrl, $productData['code']);
                    if ($imagePath) {
                        $productData['image'] = $imagePath;
                    }
                }

                $product = Product::updateOrCreate(
                    ['code' => $productData['code']],
                    $productData
                );
                
                if ($supplierId) {
                    $product->suppliers()->syncWithoutDetaching([$supplierId]);
                }
            } catch (\Exception $e) {
                Log::error("Error importando producto en Job: " . $e->getMessage());
            }
        }

        // Eliminar archivo temporal
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    private function downloadAndStoreImage($url, $code)
    {
        try {
            if (!Str::startsWith($url, ['http://', 'https://'])) {
                return null;
            }

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                $extension = 'jpg';
                
                if (Str::contains($contentType, 'image/png')) $extension = 'png';
                elseif (Str::contains($contentType, 'image/jpeg')) $extension = 'jpg';
                elseif (Str::contains($contentType, 'image/webp')) $extension = 'webp';
                elseif (Str::contains($contentType, 'image/gif')) $extension = 'gif';

                $filename = 'products/' . $code . '_' . time() . '.' . $extension;
                Storage::disk('public')->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }
}
