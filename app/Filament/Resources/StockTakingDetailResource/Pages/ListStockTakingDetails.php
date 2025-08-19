<?php

namespace App\Filament\Resources\StockTakingDetailResource\Pages;

use App\Filament\Resources\StockTakingDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockTakingDetails extends ListRecords
{
    protected static string $resource = StockTakingDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
