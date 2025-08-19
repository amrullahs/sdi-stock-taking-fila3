<?php

namespace App\Filament\Resources\StockTakingResource\Pages;

use App\Filament\Resources\StockTakingResource;
use App\Models\StockTaking;
use App\Models\StockTakingDetail;
use App\Models\ModelStructure;
use App\Models\ModelStructureDetail;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateStockTaking extends CreateRecord
{
    protected static string $resource = StockTakingResource::class;

    protected function beforeValidate(): void
    {
        $data = $this->form->getState();
        
        // Check for duplicate data
        if (isset($data['tanggal_sto']) && isset($data['model_structure_id'])) {
            $existingRecord = StockTaking::where('tanggal_sto', $data['tanggal_sto'])
                ->where('model_structure_id', $data['model_structure_id'])
                ->first();
            
            if ($existingRecord) {
                $modelStructure = ModelStructure::find($data['model_structure_id']);
                $modelName = $modelStructure ? $modelStructure->model : 'Unknown Model';
                
                Notification::make()
                    ->title('Data Sudah Ada')
                    ->body("Sudah ada transaksi STO oleh {$existingRecord->sto_user} untuk {$modelName} di tanggal ini {$data['tanggal_sto']}")
                    ->danger()
                    ->send();
                
                $this->halt();
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'open';
        $data['sto_user'] = Auth::user()->name ?? Auth::user()->email;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $stockTaking = $this->record;
        
        // Get all ModelStructureDetail based on model_structure_id
        $modelStructureDetails = ModelStructureDetail::where('model_structure_id', $stockTaking->model_structure_id)->get();
        
        // Create StockTakingDetail for each ModelStructureDetail
        foreach ($modelStructureDetails as $detail) {
            StockTakingDetail::create([
                'stock_taking_id' => $stockTaking->id,
                'model_structure_detail_id' => $detail->id,
                'storage_count' => null,
                'wip_count' => null,
                'ng_count' => null,
                'total_count' => null,
            ]);
        }
        
        Notification::make()
            ->title('Stock Taking Detail Created')
            ->body("Successfully created {$modelStructureDetails->count()} stock taking detail records.")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
