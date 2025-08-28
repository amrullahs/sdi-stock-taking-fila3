<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
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
                Tables\Columns\TextColumn::make('permissions.name')
                    ->badge()
                    ->separator(',')
                    ->label('Permissions'),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users Count'),
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
                    Tables\Actions\BulkAction::make('bulkEditPermissions')
                        ->label('Edit Permissions')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('permissions')
                                ->label('Permissions')
                                ->multiple()
                                ->relationship('permissions', 'name')
                                ->preload()
                                ->searchable()
                                ->placeholder('Select permissions to assign')
                                ->helperText('Leave empty to remove all permissions, or select specific permissions to assign.'),
                            Forms\Components\Radio::make('action_type')
                                ->label('Action Type')
                                ->options([
                                    'replace' => 'Replace all permissions',
                                    'add' => 'Add to existing permissions',
                                    'remove' => 'Remove selected permissions',
                                ])
                                ->default('replace')
                                ->required()
                                ->descriptions([
                                    'replace' => 'Replace all current permissions with selected ones',
                                    'add' => 'Add selected permissions to existing ones',
                                    'remove' => 'Remove selected permissions from existing ones',
                                ])
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $permissions = $data['permissions'] ?? [];
                            $actionType = $data['action_type'];
                            
                            foreach ($records as $role) {
                                switch ($actionType) {
                                    case 'replace':
                                        $role->syncPermissions($permissions);
                                        break;
                                    case 'add':
                                        $role->givePermissionTo($permissions);
                                        break;
                                    case 'remove':
                                        $role->revokePermissionTo($permissions);
                                        break;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Permissions updated successfully')
                                ->body('Permissions have been updated for ' . $records->count() . ' role(s).')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Edit Role Permissions')
                        ->modalDescription('Update permissions for selected roles. Choose the action type and select permissions.')
                        ->modalSubmitActionLabel('Update Permissions')
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
            'manage' => Pages\ManageRoles::route('/manage'),
        ];
    }
}
