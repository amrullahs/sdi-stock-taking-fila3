<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineStoDetailResource\Pages;
use App\Filament\Resources\LineStoDetailResource\RelationManagers;
use App\Models\LineStoDetail;
use App\Models\PeriodSto;
use App\Models\LineSto;
use App\Models\LineModelDetail;
use App\Models\Line;
use App\Exports\LineStoDetailExport;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\Section;

class LineStoDetailResource extends Resource
{
    protected static ?string $model = LineStoDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Line STO';

    protected static ?string $navigationLabel = 'Line STO Detail';

    protected static ?string $modelLabel = 'Line STO Detail';

    protected static ?string $pluralModelLabel = 'Line STO Details';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('period_id')
                    ->relationship('periodSto', 'period_sto')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('line_id')
                    ->relationship('line', 'line')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('line_model_detail_id')
                    ->relationship('lineModelDetail', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('storage_count')
                    ->label('Storage Count')
                    ->numeric()
                    ->placeholder('Enter storage count')
                    ->minValue(0),
                Forms\Components\TextInput::make('wip_count')
                    ->label('WIP Count')
                    ->numeric()
                    ->placeholder('Enter WIP count')
                    ->minValue(0),
                Forms\Components\TextInput::make('ng_count')
                    ->label('NG Count')
                    ->numeric()
                    ->placeholder('Enter NG count')
                    ->minValue(0),
                Forms\Components\TextInput::make('total_count')
                    ->label('Total Count')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('periodSto', function (Builder $query) {
                    $query->where('status', '!=', 'close');
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('periodSto.period_sto')
                    ->label('Period STO')
                    ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d-m-Y') : '-')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('line.line')
                    ->label('Line')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lineModelDetail.model_id')
                    ->label('Model')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                // Tables\Columns\TextColumn::make('lineModelDetail.qad_number')
                //     ->label('QAD')
                //     ->sortable()
                //     ->searchable()
                //     ->toggleable(),
                Tables\Columns\ImageColumn::make('lineModelDetail.image')
                    ->label('Image')
                    ->square(false)
                    ->circular(false)
                    ->width(60)
                    ->height(60)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; width: 100%; height: 100%;'
                    ])
                    ->getStateUsing(function (\App\Models\LineStoDetail $record): ?string {
                        // Akses lineModelDetail melalui relasi
                        $lineModelDetail = $record->lineModelDetail;

                        if (!$lineModelDetail) {
                            return null;
                        }

                        // Jika field image tidak null, gunakan nilai aslinya
                        if (!empty($lineModelDetail->image)) {
                            return $lineModelDetail->image;
                        }

                        // Jika image null, cari file berdasarkan nilai qad_number
                        if (!empty($lineModelDetail->qad_number)) {
                            $extensions = ['png', 'jpg', 'jpeg', 'svg'];
                            foreach ($extensions as $ext) {
                                $imagePath = storage_path("app/public/img/{$lineModelDetail->qad_number}.{$ext}");
                                if (file_exists($imagePath)) {
                                    return asset("storage/img/{$lineModelDetail->qad_number}.{$ext}");
                                }
                            }
                        }
                        // Jika tidak ada file yang ditemukan, return null untuk fallback ke defaultImageUrl
                        return null;
                    })
                    ->defaultImageUrl(url('/images/no-image.svg'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('lineModelDetail.qad_number')
                    ->label('QAD - Part Desc - Onhand')
                    ->formatStateUsing(function ($record) {
                        $qad = $record->lineModelDetail?->qad_number ?? '';
                        $partName = $record->lineModelDetail?->part_name ?? '';
                        $storage = $record->lineModelDetail?->storage ?? '';
                        $partNumber = $record->lineModelDetail?->part_number ?? '';
                        $totalOnHand = $record->total_on_hand;
                        // Make QAD number bold
                        $qadFormatted = $qad ? '<strong>' . $qad . '</strong>' : '';
                        $totalOnHandFormatted = $totalOnHand !== null ? 'OnHand = ( <strong>' . $totalOnHand . '</strong> )' : '';
                        $storageFormatted = $storage !== null ? 'Storage = ( <strong>' . $storage . '</strong> )' : '';

                        return new \Illuminate\Support\HtmlString(
                            collect([$qadFormatted, $partName, $partNumber, $storageFormatted,  $totalOnHandFormatted])
                                ->filter()
                                ->join('<br>')
                        );
                    })
                    ->searchable(['lineModelDetail.qad_number', 'lineModelDetail.part_name', 'lineModelDetail.part_number'])

                    ->visibleFrom('md')
                    ->wrap(),
                Tables\Columns\TextInputColumn::make('storage_count')
                    ->label('Storage Count')
                    ->type('number')
                    ->rules(['max:6'])
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable()
                    ->width('5px')
                    ->extraAttributes(['class' => 'text-center']),
                Tables\Columns\TextInputColumn::make('wip_count')
                    ->label('WIP Count')
                    ->type('number')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable()
                    ->width('25px'),
                Tables\Columns\TextInputColumn::make('ng_count')
                    ->label('NG Count')
                    ->type('number')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable()
                    ->width('25px'),
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total Count')
                    ->formatStateUsing(fn($record) => (
                        ($record->storage_count ?? 0) +
                        ($record->wip_count ?? 0) +
                        ($record->ng_count ?? 0)
                    ))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('period_id')
                    ->label('Period STO')
                    ->options(function () {
                        return \App\Models\LineStoDetail::with('periodSto')
                            ->get()
                            ->mapWithKeys(function ($detail) {
                                $periodSto = $detail->periodSto;
                                if ($periodSto && $periodSto->period_sto) {
                                    return [$detail->period_id => \Carbon\Carbon::parse($periodSto->period_sto)->format('d-m-Y')];
                                }
                                return [];
                            })
                            ->filter(fn($value) => !empty($value) && !is_null($value))
                            ->sort();
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('line_id')
                    ->label('Line')
                    ->options(function () {
                        return \App\Models\LineStoDetail::with('line')
                            ->get()
                            ->pluck('line.line', 'line_id')
                            ->filter(fn($value) => !empty($value) && !is_null($value))
                            ->sort();
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('lineModelDetail.model_id')
                    ->label('Model')
                    ->options(function () {
                        return \App\Models\LineStoDetail::with('lineModelDetail')
                            ->get()
                            ->pluck('lineModelDetail.model_id', 'lineModelDetail.model_id')
                            ->filter(fn($value) => !empty($value) && !is_null($value))
                            ->sortKeys();
                    })
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $value): Builder => $query->whereHas('lineModelDetail', function (Builder $query) use ($value) {
                                $query->where('model_id', $value);
                            })
                        );
                    }),
                SelectFilter::make('lineModelDetail.qad_number')
                    ->label('QAD')
                    ->options(function () {
                        return \App\Models\LineStoDetail::with('lineModelDetail')
                            ->get()
                            ->pluck('lineModelDetail.qad_number', 'lineModelDetail.qad_number')
                            ->filter(fn($value) => !empty($value) && !is_null($value))
                            ->sortKeys();
                    })
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $value): Builder => $query->whereHas('lineModelDetail', function (Builder $query) use ($value) {
                                $query->where('qad_number', $value);
                            })
                        );
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(8)
            ->filtersFormSchema(fn(array $filters): array => [
                $filters['period_id'],
                $filters['line_id'],
                $filters['lineModelDetail.model_id'],
                $filters['lineModelDetail.qad_number'],
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLineStoDetails::route('/'),
            'create' => Pages\CreateLineStoDetail::route('/create'),
            'edit' => Pages\EditLineStoDetail::route('/{record}/edit'),
        ];
    }
}
