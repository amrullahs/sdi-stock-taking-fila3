<?php

namespace App\Filament\Resources\StockTakingResource\Pages;

use App\Filament\Resources\StockTakingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockTaking extends EditRecord
{
    protected static string $resource = StockTakingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
