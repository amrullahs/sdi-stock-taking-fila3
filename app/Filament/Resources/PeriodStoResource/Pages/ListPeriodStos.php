<?php

namespace App\Filament\Resources\PeriodStoResource\Pages;

use App\Filament\Resources\PeriodStoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeriodStos extends ListRecords
{
    protected static string $resource = PeriodStoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
