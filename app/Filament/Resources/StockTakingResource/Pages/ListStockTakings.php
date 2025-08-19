<?php

namespace App\Filament\Resources\StockTakingResource\Pages;

use App\Filament\Resources\StockTakingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockTakings extends ListRecords
{
    protected static string $resource = StockTakingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
