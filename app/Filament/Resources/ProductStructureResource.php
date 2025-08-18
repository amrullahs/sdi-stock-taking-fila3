<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductStructureResource\Pages;
use App\Filament\Resources\ProductStructureResource\RelationManagers;
use App\Models\ProductStructure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductStructureResource extends Resource
{
    protected static ?string $model = ProductStructure::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('item_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Item Number'),
                Forms\Components\Select::make('category')
                    ->required()
                    ->options([
                        'MUFFLER' => 'MUFFLER',
                        'DOOR SASH' => 'DOOR SASH',
                        'CONVERTER' => 'CONVERTER',
                        'FRAME' => 'FRAME',
                    ])
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('item_number')
                    ->label('Item Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MUFFLER' => 'primary',
                        'DOOR SASH' => 'success',
                        'CONVERTER' => 'warning',
                        'FRAME' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Filter by Category')
                    ->placeholder('Select categories...')
                    ->options([
                        'MUFFLER' => 'MUFFLER',
                        'DOOR SASH' => 'DOOR SASH',
                        'CONVERTER' => 'CONVERTER',
                        'FRAME' => 'FRAME',
                    ])
                    ->multiple()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProductStructures::route('/'),
            'create' => Pages\CreateProductStructure::route('/create'),
            'edit' => Pages\EditProductStructure::route('/{record}/edit'),
        ];
    }
}
