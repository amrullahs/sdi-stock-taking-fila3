<?php

namespace App\Filament\Resources\LineStoResource\Pages;

use App\Filament\Resources\LineStoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLineSto extends EditRecord
{
    protected static string $resource = LineStoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
