<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\Supplier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Traits\Tenant\HasImportParsers;

class ImportSuppliersJob implements ShouldQueue
{
    use Queueable, HasImportParsers;

    protected $filePath;
    protected $extension;
    protected $skipDuplicates;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, string $extension, bool $skipDuplicates = true)
    {
        $this->filePath = $filePath;
        $this->extension = $extension;
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

            $validator = Validator::make($supplierData, [
                'name' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'phone' => 'required|string|max:50',
                'address' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                continue;
            }

            if ($this->skipDuplicates && Supplier::where('phone', $supplierData['phone'])->exists()) {
                continue;
            }

            try {
                Supplier::updateOrCreate(
                    ['phone' => $supplierData['phone']],
                    $supplierData
                );
            } catch (\Exception $e) {
                Log::error("Error importando proveedor en Job: " . $e->getMessage());
            }
        }

        // Eliminar archivo temporal
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
