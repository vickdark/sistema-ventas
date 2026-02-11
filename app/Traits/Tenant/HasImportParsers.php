<?php

namespace App\Traits\Tenant;

use PhpOffice\PhpSpreadsheet\IOFactory;

trait HasImportParsers
{
    private function parseFile($path, $extension)
    {
        $extension = strtolower($extension);

        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->parseExcel($path);
        }

        return $this->parseCsv($path);
    }

    private function parseExcel($path)
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        return $data;
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

    /**
     * Intenta convertir una fecha de varios formatos comunes a Y-m-d
     */
    private function transformDate($value)
    {
        if (empty($value)) return date('Y-m-d');

        // Si es un número (formato serial de Excel)
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                // Si falla, intentamos tratarlo como string
            }
        }

        $value = trim($value);
        
        // Intentar formatos comunes
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y',
            'Y/m/d',
            'd.m.Y'
        ];

        foreach ($formats as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, $value);
                if ($date) return $date->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Si nada funciona, intentar parseo automático de Carbon
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return date('Y-m-d'); // Default hoy si falla todo
        }
    }
}
