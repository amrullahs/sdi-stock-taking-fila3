<?php

namespace App\Filament\Resources\ModelStructureResource\Pages;

use App\Filament\Resources\ModelStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateModelStructure extends CreateRecord
{
    protected static string $resource = ModelStructureResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
