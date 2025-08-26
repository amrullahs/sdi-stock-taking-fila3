<?php

namespace App\Filament\Resources\LineModelDetailResource\Pages;

use App\Filament\Resources\LineModelDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLineModelDetail extends EditRecord
{
    protected static string $resource = LineModelDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
