<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('guard_name')
                    ->default('web')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->separator(',')
                    ->label('Roles'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkEditRoles')
                        ->label('Edit Roles')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('roles')
                                ->label('Roles')
                                ->multiple()
                                ->relationship('roles', 'name')
                                ->preload()
                                ->searchable()
                                ->placeholder('Select roles to assign')
                                ->helperText('Leave empty to remove from all roles, or select specific roles to assign.'),
                            Forms\Components\Radio::make('action_type')
                                ->label('Action Type')
                                ->options([
                                    'replace' => 'Replace all roles',
                                    'add' => 'Add to existing roles',
                                    'remove' => 'Remove from selected roles',
                                ])
                                ->default('replace')
                                ->required()
                                ->descriptions([
                                    'replace' => 'Replace all current roles with selected ones',
                                    'add' => 'Add selected roles to existing ones',
                                    'remove' => 'Remove from selected roles',
                                ])
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $roles = $data['roles'] ?? [];
                            $actionType = $data['action_type'];
                            
                            foreach ($records as $permission) {
                                switch ($actionType) {
                                    case 'replace':
                                        $permission->syncRoles($roles);
                                        break;
                                    case 'add':
                                        foreach ($roles as $roleId) {
                                            $role = Role::find($roleId);
                                            if ($role) {
                                                $role->givePermissionTo($permission);
                                            }
                                        }
                                        break;
                                    case 'remove':
                                        foreach ($roles as $roleId) {
                                            $role = Role::find($roleId);
                                            if ($role) {
                                                $role->revokePermissionTo($permission);
                                            }
                                        }
                                        break;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Roles updated successfully')
                                ->body('Roles have been updated for ' . $records->count() . ' permission(s).')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Edit Permission Roles')
                        ->modalDescription('Update roles for selected permissions. Choose the action type and select roles.')
                        ->modalSubmitActionLabel('Update Roles')
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
