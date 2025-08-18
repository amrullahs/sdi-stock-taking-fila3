<?php

namespace App\Filament\Resources\ProductStructureResource\Pages;

use App\Filament\Resources\ProductStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductStructures extends ListRecords
{
    protected static string $resource = ProductStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
