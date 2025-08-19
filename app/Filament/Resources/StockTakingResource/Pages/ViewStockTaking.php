<?php

namespace App\Filament\Resources\StockTakingResource\Pages;

use App\Filament\Resources\StockTakingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStockTaking extends ViewRecord
{
    protected static string $resource = StockTakingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}