<?php

namespace App\Filament\Resources\TestAutoPermissionResource\Pages;

use App\Filament\Resources\TestAutoPermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTestAutoPermission extends CreateRecord
{
    protected static string $resource = TestAutoPermissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
