<?php

namespace App\Filament\Resources\LineStoResource\RelationManagers;

use App\Models\LineModelDetail;
use App\Models\LineStoDetail;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

use Bostos\ReorderableColumns\Concerns\HasReorderableColumns;

class LineStoDetailsRelationManager extends RelationManager
{
    use HasResizableColumn;
    use HasReorderableColumns;

    protected static string $relationship = 'lineStoDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('line_model_detail_id')
                    ->label('Part')
                    ->relationship('lineModelDetail', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                // ->getOptionLabelFromRecordUsing(fn($record) => $record->part_name . ' - ' . $record->part_number),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lineModelDetail.part_name')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('lineModelDetail.model_id')
                    ->label('Model')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('lineModelDetail.image')
                    ->label('Image')
                    ->square(false)
                    ->circular(false)
                    ->width(100)
                    ->height(100)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; width: 100%; height: 100%; cursor: pointer;'
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
                    ->grow(true)
                    ->toggleable()
                    ->action(
                        Tables\Actions\Action::make('uploadImage')
                            ->label('Upload Image')
                            ->icon('heroicon-o-camera')
                            ->form([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Upload Image')
                                    ->image()
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml'])
                                    ->directory('img')
                                    ->required()
                                    ->helperText('Upload gambar untuk part ini. Format: JPG, PNG, SVG. Maksimal 2MB.'),
                            ])
                            ->action(function (array $data, $record) {
                                $lineModelDetail = $record->lineModelDetail;

                                if (!$lineModelDetail) {
                                    return;
                                }

                                // Update image path in database
                                $lineModelDetail->update([
                                    'image' => $data['image']
                                ]);

                                // Also save physical file with qad_number name if available
                                if (!empty($lineModelDetail->qad_number)) {
                                    $imagePath = $data['image'];
                                    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                                    $newFileName = "{$lineModelDetail->qad_number}.{$extension}";

                                    // Copy to public/img directory with qad_number name
                                    $sourcePath = storage_path('app/public/' . $imagePath);
                                    $destinationPath = storage_path('app/public/img/' . $newFileName);

                                    if (file_exists($sourcePath)) {
                                        // Create directory if not exists
                                        if (!file_exists(storage_path('app/public/img'))) {
                                            mkdir(storage_path('app/public/img'), 0755, true);
                                        }

                                        // Copy file
                                        copy($sourcePath, $destinationPath);
                                    }
                                }
                            })
                            ->modalWidth('md')
                            ->modalHeading('Upload Image Part')
                            ->modalDescription('Upload gambar untuk part ini.')
                            ->modalSubmitActionLabel('Upload')
                            ->modalCancelActionLabel('Batal')
                            ->color('primary')
                            ->requiresConfirmation()
                    ),
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
                    ->wrap(),
                // Tables\Columns\TextColumn::make('total_on_hand')
                //     ->label('OnHand')
                //     ->numeric()
                //     ->sortable()
                //     ->badge()
                //     ->color('info')
                //     ->getStateUsing(function ($record) {
                //         return $record->total_on_hand;
                //     }),
                Tables\Columns\TextInputColumn::make('storage_count')
                    ->label('Storage')
                    ->type('number')
                    ->placeholder('-')
                    ->rules(['integer', 'min:0'])
                    // ->extraInputAttributes([
                    //     'oninput' => 'calculateTotal(this)',
                    // ])
                    // ->columnSpanFull()
                    ->afterStateUpdated(function ($state, $record) {
                        $record->update([
                            'storage_count' => (int) $state,
                            'total_count' => (int) $state + $record->wip_count + $record->ng_count
                        ]);
                    }),
                Tables\Columns\TextInputColumn::make('wip_count')
                    ->label('WIP')
                    ->type('number')
                    ->placeholder('-')
                    ->rules(['integer', 'min:0'])
                    // ->extraInputAttributes([
                    //     'oninput' => 'calculateTotal(this)',
                    // ])
                    ->afterStateUpdated(function ($state, $record) {
                        $record->update([
                            'wip_count' => (int) $state,
                            'total_count' => $record->storage_count + (int) $state + $record->ng_count
                        ]);
                    }),
                Tables\Columns\TextInputColumn::make('ng_count')
                    ->label('NG')
                    ->type('number')
                    ->placeholder('-')
                    ->rules(['integer', 'min:0'])
                    // ->extraInputAttributes([
                    //     'oninput' => 'calculateTotal(this)',
                    // ])
                    ->afterStateUpdated(function ($state, $record) {
                        $record->update([
                            'ng_count' => (int) $state,
                            'total_count' => $record->storage_count + $record->wip_count + (int) $state
                        ]);
                    }),

                Tables\Columns\TextInputColumn::make('total_count')
                    ->label('Total')
                    ->type('number')
                    ->disabled()
                    ->getStateUsing(fn($record) => $record->storage_count + $record->wip_count + $record->ng_count)
                // ->afterStateUpdated(function ($state, $record) {
                //     $record->update(['total_count' => $state]);
                // })
                // ->extraInputAttributes([
                //     'oninput' => 'calculateTotal(this)',
                // ])
                ,
                Tables\Columns\TextInputColumn::make('remark')
                    ->label('Remark')
                    ->type('textarea')
                    ->placeholder('Add remark...')
                    ->extraInputAttributes([
                        'rows' => '2',
                        'class' => 'resize-none'
                    ])
                    ->afterStateUpdated(function ($state, $record) {
                        $record->update(['remark' => $state]);
                    }),
            ])
            ->filters([
                // SelectFilter::make('line_id')
                //     ->label('Line')
                //     ->options(function () {
                //         return LineStoDetail::with('line')
                //             ->get()
                //             ->pluck('line.line', 'line_id')
                //             ->filter(fn($value) => !empty($value) && !is_null($value))
                //             ->sort();
                //     })
                //     ->searchable()
                //     ->preload()
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query->when(
                //             $data['value'],
                //             fn(Builder $query, $value): Builder => $query->where('line_id', $value)
                //         );
                //     }),
                SelectFilter::make('lineModelDetail.model_id')
                    ->label('Model')
                    ->options(function () {
                        // Get active line_id filter value
                        $activeLineId = request()->query('tableFilters.line_id');

                        $query = LineStoDetail::with('lineModelDetail');

                        if ($activeLineId) {
                            $query->where('line_id', $activeLineId);
                        }

                        return $query->get()
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
                        // Get active line_id filter value
                        $activeLineId = request()->query('tableFilters.line_id');

                        $query = LineStoDetail::with('lineModelDetail');

                        if ($activeLineId) {
                            $query->where('line_id', $activeLineId);
                        }

                        return $query->get()
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
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                // Header actions disembunyikan
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            // ->defaultSort('lineModelDetail.model_id', 'asc');

            ->reorderableColumns('LineStoDetailModal'); // Use a unique key
    }
}
