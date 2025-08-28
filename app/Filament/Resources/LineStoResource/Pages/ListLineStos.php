<?php

namespace App\Filament\Resources\LineStoResource\Pages;

use App\Filament\Resources\LineStoResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLineStos extends ListRecords
{
    use HasResizableColumn;
    
    protected static string $resource = LineStoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
