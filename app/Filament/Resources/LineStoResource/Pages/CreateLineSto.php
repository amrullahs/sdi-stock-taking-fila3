<?php

namespace App\Filament\Resources\LineStoResource\Pages;

use App\Filament\Resources\LineStoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLineSto extends CreateRecord
{
    protected static string $resource = LineStoResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
