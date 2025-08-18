<?php

namespace App\Filament\Resources\ModelStructureResource\Pages;

use App\Filament\Resources\ModelStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModelStructures extends ListRecords
{
    protected static string $resource = ModelStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
