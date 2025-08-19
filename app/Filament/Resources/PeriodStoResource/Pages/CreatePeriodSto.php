<?php

namespace App\Filament\Resources\PeriodStoResource\Pages;

use App\Filament\Resources\PeriodStoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePeriodSto extends CreateRecord
{
    protected static string $resource = PeriodStoResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
