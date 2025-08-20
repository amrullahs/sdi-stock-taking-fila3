<?php

namespace App\Filament\Resources\StockOnHandResource\Pages;

use App\Filament\Resources\StockOnHandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockOnHand extends EditRecord
{
    protected static string $resource = StockOnHandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
