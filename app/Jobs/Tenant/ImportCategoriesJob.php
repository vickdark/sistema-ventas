<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\Category;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Traits\Tenant\HasImportParsers;


class ImportCategoriesJob implements ShouldQueue
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
            if ($index === 0 && strtolower($firstCell) === 'nombre') {
                continue;
            }

            $name = trim($row[0] ?? '');
            
            if (empty($name)) {
                continue;
            }

            if ($this->skipDuplicates && Category::where('name', $name)->exists()) {
                continue;
            }

            try {
                Category::firstOrCreate(['name' => $name]);
            } catch (\Exception $e) {
                Log::error("Error importando categoría en Job: " . $e->getMessage());
            }
        }

        // Eliminar archivo temporal
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
