<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodStoResource\Pages;
use App\Filament\Resources\PeriodStoResource\RelationManagers;
use App\Models\PeriodSto;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\FiltersLayout;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class PeriodStoResource extends Resource
{
    protected static ?string $model = PeriodSto::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Periode STO';

    protected static ?string $navigationLabel = 'Period STO';

    protected static ?int $navigationSort = -20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Period STO Information')
                    ->schema([
                        Forms\Components\DatePicker::make('period_sto')
                            ->label('Period STO')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection()
                            ->disabled(fn(string $operation) => $operation === 'edit') // Make field disabled when editing
                            ->rules([
                                function (string $operation) {
                                    return function (string $attribute, $value, \Closure $fail) use ($operation) {
                                        if (!$value) return;

                                        // Only check for duplicate period when creating (not when editing)
                                        if ($operation === 'create') {
                                            $query = PeriodSto::where('period_sto', $value);

                                            if ($query->exists()) {
                                                $date = Carbon::parse($value)->format('d/m/Y');
                                                $fail("Sudah ada period untuk tanggal {$date}.");
                                            }
                                        }
                                    };
                                }
                            ]),
                        Forms\Components\TextInput::make('site')
                            ->label('Site')
                            ->required()
                            ->maxLength(255)
                            ->default('7000'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'open' => 'Open',
                                'close' => 'Close',
                            ])
                            ->default('open')
                            ->required(),
                        Forms\Components\TextInput::make('created_by')
                            ->label('Created By')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Auto-filled with current user name'),
                        Forms\Components\FileUpload::make('excel_file')
                            ->label('Upload Excel File')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->required(fn(string $operation) => $operation === 'create') // Only required when creating
                            ->disk('public')
                            ->directory('excel-imports')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(10240) // 10MB max
                            ->helperText(
                                fn(string $operation) =>
                                $operation === 'create'
                                    ? 'Upload Excel file dengan kolom: item_number, desc, location, lot, ref, status, qty_on_hand, confirming, created, total_on_hand. Download template di halaman list Period STO. Max size: 10MB'
                                    : 'Upload Excel file opsional. Kosongkan jika tidak ingin mengupdate data stock. Max size: 10MB'
                            )
                            ->dehydrated(true) // Changed to true to include in form data
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    Log::info('FileUpload afterStateUpdated', [
                                        'state' => $state,
                                        'state_type' => gettype($state),
                                        'is_array' => is_array($state),
                                        'state_content' => is_array($state) ? $state : [$state]
                                    ]);
                                }
                            }),
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
                Tables\Columns\TextColumn::make('period_sto')
                    ->label('Period STO')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('site')
                    ->label('Site')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'success',
                        'close' => 'danger',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_template_semicolon')
                    ->label('Download Template (Semicolon ;)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn() => asset('storage/excel-imports/template_stock_on_hand.csv'))
                    ->openUrlInNewTab()
                    ->tooltip('Download template CSV dengan delimiter semicolon (;)'),
                Tables\Actions\Action::make('download_template_comma')
                    ->label('Download Template (Comma ,)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn() => asset('storage/excel-imports/template_stock_on_hand_comma.csv'))
                    ->openUrlInNewTab()
                    ->tooltip('Download template CSV dengan delimiter comma (,)'),
            ])
            ->filters([
                DateRangeFilter::make('period_sto'),
                Tables\Filters\SelectFilter::make('site')
                    ->label('Site')
                    ->options(fn(): array => PeriodSto::distinct()->pluck('site', 'site')->filter(fn($value) => !empty($value) && !is_null($value))->sort()->toArray())
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                RelationManagerAction::make('lineStos')
                    ->label('View Line STO')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->modalHeading(fn($record) => 'Line STO - ' . $record->period_sto->format('d/m/Y') . ' (' . $record->site . ')')
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->relationManager(RelationManagers\LineStosRelationManager::make()),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
            RelationManagers\LineStosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriodStos::route('/'),
            'create' => Pages\CreatePeriodSto::route('/create'),
            'view' => Pages\ViewPeriodSto::route('/{record}'),
            'edit' => Pages\EditPeriodSto::route('/{record}/edit'),
        ];
    }
}
