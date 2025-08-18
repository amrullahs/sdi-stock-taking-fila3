<?php

namespace App\Filament\Resources\TestAutoPermissionResource\Pages;

use App\Filament\Resources\TestAutoPermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestAutoPermission extends EditRecord
{
    protected static string $resource = TestAutoPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
