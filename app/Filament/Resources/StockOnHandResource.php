<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOnHandResource\Pages;
use App\Filament\Resources\StockOnHandResource\RelationManagers;
use App\Models\StockOnHand;
use App\Models\PeriodSto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockOnHandResource extends Resource
{
    protected static ?string $model = StockOnHand::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Periode STO';
    
    protected static ?string $navigationLabel = 'Stock On Hand';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Stock On Hand Information')
                    ->schema([
                        Forms\Components\Select::make('period_sto_id')
                            ->label('Period STO')
                            ->relationship('periodSto', 'period_sto')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('item_number')
                            ->label('Item Number')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('desc')
                            ->label('Description')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lot')
                            ->label('Lot')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('ref')
                            ->label('Reference')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('qty_on_hand')
                            ->label('Qty On Hand')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('confirming')
                            ->label('Confirming')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('created')
                            ->label('Created Date')
                            ->native(false),
                        Forms\Components\TextInput::make('total_on_hand')
                            ->label('Total On Hand')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('periodSto'))
            ->columns([
                // Default visible columns
                Tables\Columns\TextColumn::make('periodSto.period_sto')
                    ->label('Period STO')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('item_number')
                    ->label('Item Number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_on_hand')
                    ->label('Total On Hand')
                    ->numeric()
                    ->sortable(),
                
                // Toggleable columns (hidden by default)
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('desc')
                    ->label('Description')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('lot')
                    ->label('Lot')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('qty_on_hand')
                    ->label('Qty On Hand')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('uploaded')
                    ->label('Uploaded')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period_sto_id')
                    ->label('Period STO')
                    ->relationship('periodSto', 'period_sto')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('uploaded')
                    ->form([
                        Forms\Components\DatePicker::make('uploaded_from')
                            ->label('Uploaded From'),
                        Forms\Components\DatePicker::make('uploaded_until')
                            ->label('Uploaded Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['uploaded_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('uploaded', '>=', $date),
                            )
                            ->when(
                                $data['uploaded_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('uploaded', '<=', $date),
                            );
                    }),
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
            ->defaultSort('uploaded', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100]);
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
            'index' => Pages\ListStockOnHands::route('/'),
            'create' => Pages\CreateStockOnHand::route('/create'),
            'edit' => Pages\EditStockOnHand::route('/{record}/edit'),
        ];
    }
}
