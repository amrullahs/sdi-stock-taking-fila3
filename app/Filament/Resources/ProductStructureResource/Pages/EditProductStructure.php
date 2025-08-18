<?php

namespace App\Filament\Resources\ProductStructureResource\Pages;

use App\Filament\Resources\ProductStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductStructure extends EditRecord
{
    protected static string $resource = ProductStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
