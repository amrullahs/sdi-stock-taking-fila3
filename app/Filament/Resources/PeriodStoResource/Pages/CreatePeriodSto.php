<?php

namespace App\Filament\Resources\PeriodStoResource\Pages;

use App\Filament\Resources\PeriodStoResource;
use App\Imports\StockOnHandImport;
use App\Imports\StockOnHandImportComma;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreatePeriodSto extends CreateRecord
{
    protected static string $resource = PeriodStoResource::class;

    protected $excelFilePath;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Debug: Log all form data
        Log::info('mutateFormDataBeforeCreate called', [
            'all_data' => $data,
            'data_keys' => array_keys($data),
            'excel_file_isset' => isset($data['excel_file']),
            'excel_file_value' => $data['excel_file'] ?? 'not_set',
            'excel_file_type' => isset($data['excel_file']) ? gettype($data['excel_file']) : 'not_set'
        ]);
        
        // Set created_by to current user
        $data['created_by'] = Auth::user()->name;
        
        // Store excel file path temporarily
        if (isset($data['excel_file']) && $data['excel_file']) {
            // Handle both string and array cases
            if (is_array($data['excel_file'])) {
                $this->excelFilePath = $data['excel_file'][0] ?? null;
            } else {
                $this->excelFilePath = $data['excel_file'];
            }
            
            unset($data['excel_file']); // Remove from data as it's not a database field
            
            // Debug: Log file path with more details
            Log::info('Excel file uploaded', [
                'excel_file_path' => $this->excelFilePath,
                'file_type' => gettype($this->excelFilePath),
                'is_string' => is_string($this->excelFilePath),
                'storage_disk' => 'public',
                'full_path_will_be' => $this->excelFilePath ? Storage::disk('public')->path($this->excelFilePath) : 'null'
            ]);
        } else {
            Log::warning('No Excel file uploaded or file is empty', [
                'data_keys' => array_keys($data),
                'excel_file_isset' => isset($data['excel_file']),
                'excel_file_value' => $data['excel_file'] ?? 'not_set'
            ]);
        }
        
        return $data;
    }

    /**
     * Detect CSV delimiter by checking first few lines
     */
    private function detectCsvDelimiter($filePath): string
    {
        if (!file_exists($filePath)) {
            return ';'; // default to semicolon
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ';'; // default to semicolon
        }

        // Read first line to detect delimiter
        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine) {
            return ';'; // default to semicolon
        }

        // Count occurrences of potential delimiters
        $semicolonCount = substr_count($firstLine, ';');
        $commaCount = substr_count($firstLine, ',');

        // Return the delimiter with more occurrences
        return ($semicolonCount >= $commaCount) ? ';' : ',';
    }

    protected function afterCreate(): void
    {
        // Check if this is create or edit operation
        $isEdit = $this->record->wasRecentlyCreated === false;
        
        // Import Excel data after PeriodSto is created with transaction
        if (isset($this->excelFilePath) && $this->excelFilePath) {
            DB::beginTransaction();
            try {
                // Get the actual file path
                $filePath = Storage::disk('public')->path($this->excelFilePath);

                // Additional debugging
                $storageExists = Storage::disk('public')->exists($this->excelFilePath);
                $fileSize = $storageExists ? Storage::disk('public')->size($this->excelFilePath) : 0;

                // Debug logging with more details
                Log::info('Attempting to import file', [
                    'excel_file_path' => $this->excelFilePath,
                    'full_path' => $filePath,
                    'file_exists_filesystem' => file_exists($filePath),
                    'storage_exists' => $storageExists,
                    'file_size' => $fileSize,
                    'storage_disk' => 'public',
                    'storage_root' => Storage::disk('public')->path(''),
                    'period_sto_id' => $this->record->id,
                    'directory_contents' => Storage::disk('public')->files('excel-imports')
                ]);

                // Check if file exists using Storage facade first
                if (!$storageExists) {
                    throw new \Exception('File tidak ditemukan di storage: ' . $this->excelFilePath);
                }

                // Double check with filesystem
                if (!file_exists($filePath)) {
                    throw new \Exception('File tidak ditemukan di filesystem: ' . $filePath);
                }

                // Detect file type and delimiter for CSV files
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                
                if ($fileExtension === 'csv') {
                    // Detect CSV delimiter
                    $delimiter = $this->detectCsvDelimiter($filePath);
                    
                    // Use appropriate import class based on delimiter
                    if ($delimiter === ';') {
                        Excel::import(
                            new StockOnHandImport($this->record->id),
                            $filePath
                        );
                    } else {
                        Excel::import(
                            new StockOnHandImportComma($this->record->id),
                            $filePath
                        );
                    }
                } else {
                    // For Excel files, use default import (semicolon)
                    Excel::import(
                        new StockOnHandImport($this->record->id),
                        $filePath
                    );
                }

                // Count imported records
                $importedCount = \App\Models\StockOnHand::where('period_sto_id', $this->record->id)->count();

                // If no records imported, throw exception
                if ($importedCount === 0) {
                    throw new \Exception('Tidak ada data yang berhasil diimpor dari file Excel.');
                }

                // Delete the uploaded file after import
                Storage::disk('public')->delete($this->excelFilePath);

                // Commit transaction if everything is successful
                DB::commit();

                Notification::make()
                    ->title('Import Excel Berhasil')
                    ->body("Data stock on hand berhasil diimpor. Total: {$importedCount} records.")
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                // Rollback transaction on any error
                DB::rollback();
                
                // Delete the PeriodSto record that was created
                $this->record->delete();
                
                Log::error('Excel import failed - PeriodSto rolled back', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'excel_file_path' => $this->excelFilePath ?? 'null',
                    'period_sto_id' => $this->record->id,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Provide more specific error messages
                $errorMessage = $e->getMessage();
                if (strpos($errorMessage, 'File tidak ditemukan') !== false) {
                    $errorMessage = 'File Excel tidak ditemukan di server. Pastikan file berhasil di-upload.';
                } elseif (strpos($errorMessage, 'permission') !== false || strpos($errorMessage, 'Permission') !== false) {
                    $errorMessage = 'Tidak ada izin untuk mengakses file Excel. Hubungi administrator.';
                } elseif (strpos($errorMessage, 'format') !== false || strpos($errorMessage, 'Format') !== false) {
                    $errorMessage = 'Format file Excel tidak valid. Pastikan menggunakan template yang benar.';
                } else {
                    $errorMessage = 'Error saat import file Excel: ' . $errorMessage;
                }
                
                Notification::make()
                    ->title('Import Excel Gagal - Data Periode STO Dibatalkan')
                    ->body($errorMessage . ' Data periode STO telah dihapus karena upload gagal.')
                    ->danger()
                    ->persistent() // Make it persistent so user can read the full message
                    ->send();
                    
                // Redirect to index page after error
                $this->redirect($this->getResource()::getUrl('index'));
            }
        } else {
            Log::warning('No Excel file uploaded', [
                'excel_file_path' => $this->excelFilePath ?? 'null',
                'is_edit' => $isEdit ?? false
            ]);
            
            // Different notification for edit vs create
            if ($isEdit) {
                // For edit, no Excel upload is optional, so just log and continue
                Log::info('Edit operation completed without Excel file upload', [
                    'period_sto_id' => $this->record->id,
                    'message' => 'Data periode STO berhasil diupdate tanpa upload file Excel'
                ]);
                // No notification needed for edit when no file uploaded
            } else {
                // For create, warn about missing Excel file
                Notification::make()
                    ->title('File Excel Tidak Ditemukan')
                    ->body('File Excel tidak di-upload atau path tidak valid. Pastikan Anda memilih file Excel sebelum menyimpan data.')
                    ->warning()
                    ->persistent()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('download_template_semicolon')
                            ->label('Download Template (Semicolon ;)')
                            ->url(asset('storage/excel-imports/template_stock_on_hand.csv'))
                            ->openUrlInNewTab(),
                        \Filament\Notifications\Actions\Action::make('download_template_comma')
                            ->label('Download Template (Comma ,)')
                            ->url(asset('storage/excel-imports/template_stock_on_hand_comma.csv'))
                            ->openUrlInNewTab()
                    ])
                    ->send();
            }
        }
    }
}
