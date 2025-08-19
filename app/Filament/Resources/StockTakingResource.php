<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTakingResource\Pages;
use App\Filament\Resources\StockTakingResource\RelationManagers;
use App\Models\ModelStructure;
use App\Models\StockTaking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockTakingResource extends Resource
{
    protected static ?string $model = StockTaking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = 'Stock Taking';
    
    protected static ?string $navigationLabel = 'Stock Taking';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Stock Taking Information')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_sto')
                            ->label('Tanggal STO')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('model_structure_id')
                            ->label('Model Structure')
                            ->relationship('modelStructure', 'model')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $modelStructure = ModelStructure::find($state);
                                    if ($modelStructure) {
                                        $set('model', $modelStructure->model);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Tracking Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('sto_start_at')
                            ->label('Start Time'),
                        Forms\Components\DateTimePicker::make('sto_submit_at')
                            ->label('Submit Time'),
                        Forms\Components\DateTimePicker::make('sto_update_at')
                            ->label('Update Time'),
                        Forms\Components\TextInput::make('progress')
                            ->label('Progress (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_sto')
                    ->label('Tanggal STO')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modelStructure.model')
                    ->label('Model Structure')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => StockTaking::STATUS_OPEN,
                        'warning' => StockTaking::STATUS_ON_PROGRESS,
                        'success' => StockTaking::STATUS_CLOSE,
                    ])
                    ->formatStateUsing(fn (string $state): string => StockTaking::getStatuses()[$state] ?? $state),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : '0%'),
                Tables\Columns\TextColumn::make('sto_user')
                    ->label('STO User')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sto_start_at')
                    ->label('Start Time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sto_submit_at')
                    ->label('Submit Time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StockTaking::getStatuses()),
                SelectFilter::make('model_structure_id')
                    ->label('Model Structure')
                    ->relationship('modelStructure', 'model')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('tanggal_sto')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_sto', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_sto', '<=', $date),
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
            'index' => Pages\ListStockTakings::route('/'),
            'create' => Pages\CreateStockTaking::route('/create'),
            'view' => Pages\ViewStockTaking::route('/{record}'),
            'edit' => Pages\EditStockTaking::route('/{record}/edit'),
        ];
    }
}
