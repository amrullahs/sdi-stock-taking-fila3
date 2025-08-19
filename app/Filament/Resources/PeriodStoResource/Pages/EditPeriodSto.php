<?php

namespace App\Filament\Resources\PeriodStoResource\Pages;

use App\Filament\Resources\PeriodStoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeriodSto extends EditRecord
{
    protected static string $resource = PeriodStoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
