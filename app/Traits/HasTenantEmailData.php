<?php

namespace App\Traits;

use Illuminate\Support\Facades\Lang;

trait HasTenantEmailData
{
    /**
     * Obtiene los datos del tenant para personalizar los correos electrónicos.
     *
     * @return array
     */
    protected function getTenantEmailData()
    {
        $tenant = function_exists('tenant') ? tenant() : null;
        $businessName = $tenant ? ($tenant->business_name ?? $tenant->id) : config('app.name');
        $logoPath = null;

        if ($tenant && $tenant->logo) {
            $logoRelativePath = $tenant->logo;
            
            $possiblePaths = [
                storage_path('app/public/' . $logoRelativePath),
                public_path('storage/' . $logoRelativePath),
                storage_path($logoRelativePath),
                base_path('public/storage/' . $logoRelativePath)
            ];

            foreach ($possiblePaths as $path) {
                $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
                if (file_exists($normalizedPath)) {
                    $logoPath = $normalizedPath;
                    break;
                }
            }
        }

        return [
            'tenant' => $tenant,
            'businessName' => $businessName,
            'logoPath' => $logoPath, // Enviamos la ruta física
        ];
    }
}
