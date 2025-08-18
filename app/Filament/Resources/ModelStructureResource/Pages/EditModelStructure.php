<?php

namespace App\Filament\Resources\ModelStructureResource\Pages;

use App\Filament\Resources\ModelStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModelStructure extends EditRecord
{
    protected static string $resource = ModelStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
