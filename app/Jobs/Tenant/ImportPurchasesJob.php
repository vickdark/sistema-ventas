<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\Product;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\PurchaseItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Traits\Tenant\HasImportParsers;

class ImportPurchasesJob implements ShouldQueue
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
        ini_set('memory_limit', '256M');
        
        $fullPath = storage_path('app/' . $this->filePath);
        
        if (!file_exists($fullPath)) {
            Log::error("Archivo de importación no encontrado: " . $fullPath);
            return;
        }

        $data = $this->parseFile($fullPath, $this->extension);

        foreach ($data as $index => $row) {
            $firstCell = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0] ?? ''));
            if ($index === 0 && (strtolower($firstCell) === 'codigo_producto' || strtolower($firstCell) === 'product_code')) {
                continue;
            }

            $productCode = trim($row[0] ?? '');
            $supplierCompany = trim($row[1] ?? '');
            $quantity = trim($row[2] ?? 0);
            $price = trim($row[3] ?? 0);
            $nroCompra = trim($row[4] ?? '');
            $voucher = trim($row[5] ?? '');
            $purchaseDate = trim($row[6] ?? date('Y-m-d'));

            $product = Product::where('code', $productCode)->first();
            $supplier = Supplier::where('company', $supplierCompany)->first();

            if (!$product || !$supplier) {
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
                'user_id' => $this->userId
            ];

            $validator = Validator::make($purchaseData, [
                'nro_compra' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                continue;
            }

            if ($this->skipDuplicates && Purchase::where('nro_compra', $nroCompra)->exists()) {
                continue;
            }

            // Validar stock máximo
            if (($product->stock + $quantity) > $product->max_stock) {
                Log::warning("Fila " . ($index + 1) . ": La cantidad excede el stock máximo para el producto {$productCode}.");
                continue;
            }

            try {
                DB::transaction(function () use ($purchaseData, $product) {
                    $purchase = Purchase::create($purchaseData);
                    
                    // Actualizar stock del producto
                    $product->increment('stock', $purchaseData['quantity']);
                });
            } catch (\Exception $e) {
                Log::error("Error importando compra en Job: " . $e->getMessage());
            }
        }

        // Eliminar archivo temporal
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
