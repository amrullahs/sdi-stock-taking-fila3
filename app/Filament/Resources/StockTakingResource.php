<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTakingResource\Pages;
use App\Filament\Resources\StockTakingResource\RelationManagers;
use App\Models\ModelStructure;
use App\Models\StockTaking;
use App\Models\StockTakingDetail;
use App\Models\PeriodSto;
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
                        Forms\Components\Select::make('period_id')
                            ->label('Period STO')
                            ->options(PeriodSto::all()->pluck('period_sto', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->rules([
                                function (callable $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $modelStructureId = $get('model_structure_id');
                                        if ($modelStructureId && $value) {
                                            $exists = \App\Models\StockTaking::where('period_id', $value)
                                                ->where('model_structure_id', $modelStructureId)
                                                ->exists();
                                            if ($exists) {
                                                $fail('Kombinasi Period STO dan Model Structure sudah ada.');
                                            }
                                        }
                                    };
                                }
                            ]),
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
                            })
                            ->rules([
                                function (callable $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $periodId = $get('period_id');
                                        if ($periodId && $value) {
                                            $exists = \App\Models\StockTaking::where('period_id', $periodId)
                                                ->where('model_structure_id', $value)
                                                ->exists();
                                            if ($exists) {
                                                $fail('Kombinasi Period STO dan Model Structure sudah ada.');
                                            }
                                        }
                                    };
                                }
                            ]),
                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Tracking Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('sto_start_at')
                            ->label('Start Time')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Auto-updated when first data is entered'),
                        Forms\Components\DateTimePicker::make('sto_submit_at')
                            ->label('Submit Time')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Set when stock taking is submitted'),
                        Forms\Components\DateTimePicker::make('sto_update_at')
                            ->label('Update Time')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Auto-updated on every data change'),
                        Forms\Components\TextInput::make('progress')
                            ->label('Progress (%)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->suffix('%')
                            ->helperText('Auto-calculated based on completed entries'),
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
                Tables\Columns\TextColumn::make('periodSto.period_sto')
                    ->label('Period STO')
                    ->searchable()
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
                SelectFilter::make('period_id')
                    ->label('Period STO')
                    ->relationship('periodSto', 'period_sto')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('sto_detail')
                    ->label('STO Detail')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'STO Detail - ' . $record->modelStructure->model . ' (' . ($record->periodSto->period_sto ?? 'N/A') . ')')
                    ->modalContent(fn ($record) => view('filament.modals.sto-detail', [
                        'stockTakingId' => $record->id,
                        'modelName' => $record->modelStructure->model,
                        'tanggalSto' => $record->periodSto->period_sto ?? 'N/A',
                    ]))
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
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
