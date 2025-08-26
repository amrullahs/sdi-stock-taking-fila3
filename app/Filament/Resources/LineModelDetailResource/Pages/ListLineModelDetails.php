<?php

namespace App\Filament\Resources\LineModelDetailResource\Pages;

use App\Filament\Resources\LineModelDetailResource;
use App\Imports\LineModelDetailImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListLineModelDetails extends ListRecords
{
    protected static string $resource = LineModelDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    return response()->download(
                        storage_path('app/public/templates/line_model_detail_template.csv'),
                        'line_model_detail_template.csv'
                    );
                }),
            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('Upload Excel File')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->required()
                        ->helperText('Upload file Excel/CSV dengan format: line, model, qad_number, part_name, part_number, supplier, std_packing, storage, image. Download template untuk contoh format.')
                ])
                ->action(function (array $data) {
                    try {
                        $file = $data['file'];
                        Excel::import(new LineModelDetailImport, $file);
                        
                        Notification::make()
                            ->title('Import berhasil!')
                            ->body('Data Line Model Detail berhasil diimport dari Excel.')
                            ->success()
                            ->send();
                            
                        // Refresh the page to show new data
                        $this->redirect(static::getUrl());
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import gagal!')
                            ->body('Error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->modalHeading('Import Line Model Detail dari Excel')
                ->modalDescription('Upload file Excel untuk mengimport data Line Model Detail secara bulk.')
                ->modalSubmitActionLabel('Import')
                ->modalCancelActionLabel('Batal'),
        ];
    }
}
