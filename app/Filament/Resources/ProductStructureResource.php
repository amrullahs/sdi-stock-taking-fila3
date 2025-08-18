<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductStructureResource\Pages;
use App\Models\ProductStructure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
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
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255)
                    ->label('Model'),
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
                    ->tooltip(fn ($record): string => $record->id),
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
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
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
                Tables\Filters\SelectFilter::make('model')
                    ->label('Filter by Model')
                    ->placeholder('Select models...')
                    ->options(function () {
                        return \App\Models\ProductStructure::distinct()
                            ->pluck('model', 'model')
                            ->filter()
                            ->sort()
                            ->toArray();
                    })
                    ->multiple()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_bom')
                    ->label('View BOM')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'BOM Details - ' . $record->item_number)
                    ->modalContent(fn ($record) => view('filament.modals.bom-table', ['record' => $record]))
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
            ])
            ->bulkActions([
                // Bulk actions removed for security - managed by Filament Shield
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->recordAction(null);
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
