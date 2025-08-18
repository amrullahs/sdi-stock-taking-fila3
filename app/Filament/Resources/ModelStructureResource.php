<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModelStructureResource\Pages;
use App\Filament\Resources\ModelStructureResource\RelationManagers;
use App\Models\ModelStructure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModelStructureResource extends Resource
{
    protected static ?string $model = ModelStructure::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?string $navigationLabel = 'Model Structure';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('model')
                    ->label('Model')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter model name'),
                Forms\Components\TextInput::make('line')
                    ->label('Line')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter line name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->copyable()
                    ->limit(12)
                    ->tooltip(fn ($record): string => $record->id),
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('line')
                    ->label('Line')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListModelStructures::route('/'),
            'create' => Pages\CreateModelStructure::route('/create'),
            'view' => Pages\ViewModelStructure::route('/{record}'),
            'edit' => Pages\EditModelStructure::route('/{record}/edit'),
        ];
    }
}
