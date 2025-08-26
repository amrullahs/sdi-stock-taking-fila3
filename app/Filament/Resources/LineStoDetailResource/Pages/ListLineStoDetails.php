<?php

namespace App\Filament\Resources\LineStoDetailResource\Pages;

use App\Filament\Resources\LineStoDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLineStoDetails extends ListRecords
{
    protected static string $resource = LineStoDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
