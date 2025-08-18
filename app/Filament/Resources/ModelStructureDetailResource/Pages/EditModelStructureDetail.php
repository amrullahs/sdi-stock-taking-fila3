<?php

namespace App\Filament\Resources\ModelStructureDetailResource\Pages;

use App\Filament\Resources\ModelStructureDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModelStructureDetail extends EditRecord
{
    protected static string $resource = ModelStructureDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
