<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Central\Tenant;
use Illuminate\Support\Facades\Log;

class BackupDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-databases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un backup individual de cada base de datos y lo sincroniza con Google Drive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Directorio Permanente fuera de la carpeta del proyecto por seguridad
        $serverDir = '/home/mambacode/backups_db_sistema_ventas_laravel';
        if (!is_dir($serverDir)) {
            mkdir($serverDir, 0755, true);
        }

        // Obtener credenciales desde la conexión central
        $user = config('database.connections.central.username');
        $pass = config('database.connections.central.password');
        $host = config('database.connections.central.host');

        $this->info("Iniciando proceso de backup (Local y Drive)...");

        // 2. Backup de la Base de Datos Central
        $centralDb = config('database.connections.central.database');
        $this->doBackup($centralDb, $centralDb, $serverDir, $user, $pass, $host);

        // 3. Backup de cada Inquilino
        Tenant::all()->each(function ($tenant) use ($serverDir, $user, $pass, $host) {
            $dbName = $tenant->tenancy_db_name ?? 'tenant_' . $tenant->id;
            $this->doBackup($dbName, $dbName, $serverDir, $user, $pass, $host);
        });

        // 4. Sincronizar con Google Drive en una carpeta específica para este proyecto
        $this->info("Sincronizando con Google Drive (Carpeta: backups_db_sistema_ventas_laravel)...");
        // Usamos 'copy' para que rclone suba y sobrescriba los archivos con el mismo nombre
        $command = "rclone copy $serverDir gdrive:backups_db_sistema_ventas_laravel";
        
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            $this->info("¡Backup completado! Archivos guardados en: $serverDir y en Drive.");
        } else {
            $this->error("Error en la sincronización con rclone. Código: $resultCode");
            Log::error("Fallo rclone backup: " . implode("\n", $output));
        }
    }

    /**
     * Ejecuta el comando mysqldump para una base de datos específica.
     */
    private function doBackup($dbName, $label, $dir, $user, $pass, $host)
    {
        // Usamos el nombre nato de la DB + fecha para el archivo
        // Al incluir la fecha del día (Y-m-d), se sobrescribirá cada 6 horas del mismo día.
        $date = now()->format('Y-m-d');
        $fileName = "{$label}-{$date}.sql";
        $filePath = "$dir/$fileName";

        $this->comment("Procesando: $dbName -> $fileName");
        
        // El parámetro --no-tablespaces suele ser necesario en algunos entornos para evitar errores de permisos
        $cmd = "mysqldump --user=\"$user\" --password=\"$pass\" --host=\"$host\" --no-tablespaces $dbName > \"$filePath\"";
        
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0) {
            $this->error("Error al respaldar la DB: $dbName");
            Log::error("Error mysqldump DB: $dbName");
        }
    }
}