<?php

namespace App\Filament\Resources\StockTakingDetailResource\Pages;

use App\Filament\Resources\StockTakingDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockTakingDetail extends EditRecord
{
    protected static string $resource = StockTakingDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
