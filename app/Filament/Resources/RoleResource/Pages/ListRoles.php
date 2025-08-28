<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('managePermissions')
                ->label('Manage Permissions')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info')
                ->url(fn () => static::getResource()::getUrl('manage'))
                ->tooltip('Manage role permissions with checkboxlist interface'),
        ];
    }
}
