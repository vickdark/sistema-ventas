<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Traits\Tenant\HasImportParsers;
class ImportClientsJob implements ShouldQueue
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

            $clientData = [
                'name' => trim($row[0] ?? ''),
                'nit_ci' => trim($row[1] ?? ''),
                'phone' => trim($row[2] ?? ''),
                'email' => trim($row[3] ?? '')
            ];

            $validator = Validator::make($clientData, [
                'name' => 'required|string|max:255',
                'nit_ci' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255'
            ]);

            if ($validator->fails()) {
                continue;
            }

            if ($this->skipDuplicates && Client::where('nit_ci', $clientData['nit_ci'])->exists()) {
                continue;
            }

            try {
                Client::updateOrCreate(
                    ['nit_ci' => $clientData['nit_ci']],
                    $clientData
                );
            } catch (\Exception $e) {
                Log::error("Error importando cliente en Job: " . $e->getMessage());
            }
        }

        // Eliminar archivo temporal
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
