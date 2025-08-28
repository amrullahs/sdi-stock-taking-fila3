<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineStoResource\Pages;
use App\Filament\Resources\LineStoResource\RelationManagers;
use App\Models\LineSto;
use App\Models\Line;
use App\Models\PeriodSto;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Filament\Support\Enums\MaxWidth;

class LineStoResource extends Resource
{
    protected static ?string $model = LineSto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Line STO';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('period_id')
                    ->label('Period STO')
                    ->options(function () {
                        return PeriodSto::where('status', '!=', 'close')
                            ->orderBy('period_sto', 'desc')
                            ->get()
                            ->mapWithKeys(function ($period) {
                                return [$period->id => $period->period_sto->format('d/m/Y') . ' - ' . $period->site];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih Period STO')
                    ->disabled(fn($record) => $record && $record->status === 'onprogress'),

                Forms\Components\Select::make('line_id')
                    ->label('Line')
                    ->options(Line::all()->pluck('line', 'id'))
                    ->searchable()
                    ->required()
                    ->disabled(fn($record) => $record && $record->status === 'onprogress'),

                Forms\Components\TextInput::make('created_by')
                    ->label('Created By')
                    ->maxLength(255)
                    ->default(fn() => Auth::user()?->name ?? Auth::user()?->email)
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\TextInput::make('site')
                    ->label('Site')
                    ->maxLength(255)
                    ->default('7000')
                    ->required()
                    ->disabled(fn($record) => $record && $record->status === 'onprogress'),

                Forms\Components\DatePicker::make('tanggal_sto')
                    ->label('Tanggal STO')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->default(now()->format('Y-m-d'))
                    ->disabled()
                    ->dehydrated()
                    ->closeOnDateSelection(),

                Forms\Components\DateTimePicker::make('sto_start_at')
                    ->label('STO Start At')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\DateTimePicker::make('sto_submit_at')
                    ->label('STO Submit At')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\DateTimePicker::make('sto_update_at')
                    ->label('STO Update At')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(function ($record) {
                        if ($record && $record->status === 'onprogress') {
                            return ['close' => 'Close'];
                        }
                        return [
                            'open' => 'Open',
                            'onprogress' => 'On Progress',
                            'close' => 'Close',
                        ];
                    })
                    ->default('open')
                    ->disabled(fn($record) => !$record || $record->status !== 'onprogress')
                    ->dehydrated(),

                Forms\Components\TextInput::make('progress')
                    ->label('Progress (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->suffix('%')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('periodSto.period_sto')
                    ->label('Period STO')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('line.line')
                    ->label('Line')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('site')
                    ->label('Site')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tanggal_sto')
                    ->label('Tanggal STO')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sto_start_at')
                    ->label('STO Start')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sto_submit_at')
                    ->label('STO Submit')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sto_update_at')
                    ->label('STO Update')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'danger',
                        'onprogress' => 'warning',
                        'close' => 'success',
                    })
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->formatStateUsing(fn($state) => $state . '%')
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
                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Period STO')
                    ->relationship('periodSto', 'period_sto')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => \Carbon\Carbon::parse($record->period_sto)->format('d/m/Y') . ' - ' . $record->site),

                Tables\Filters\SelectFilter::make('line_id')
                    ->label('Line')
                    ->relationship('line', 'line')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'onprogress' => 'On Progress',
                        'close' => 'Close',
                    ])
                    ->searchable(),
            ])
            ->actions([
                RelationManagerAction::make('lineStoDetails')
                    ->label('Detail')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('info')
                    ->modalHeading(false)
                    ->modalWidth(MaxWidth::Screen)
                    ->modalCancelActionLabel('✕')
                    ->relationManager(RelationManagers\LineStoDetailsRelationManager::make()),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LineStoDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLineStos::route('/'),
            'create' => Pages\CreateLineSto::route('/create'),
            'edit' => Pages\EditLineSto::route('/{record}/edit'),
        ];
    }
}
