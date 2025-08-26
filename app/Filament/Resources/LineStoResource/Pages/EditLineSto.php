<?php

namespace App\Filament\Resources\LineStoResource\Pages;

use App\Filament\Resources\LineStoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLineSto extends EditRecord
{
    protected static string $resource = LineStoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validasi progress 100% jika status diubah ke 'close'
        if (isset($data['status']) && $data['status'] === 'close') {
            $progress = $this->record->progress;
            
            if ($progress < 100) {
                Notification::make()
                    ->title('Progress STO harus 100% untuk Closing Status')
                    ->danger()
                    ->send();
                
                $this->halt();
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
