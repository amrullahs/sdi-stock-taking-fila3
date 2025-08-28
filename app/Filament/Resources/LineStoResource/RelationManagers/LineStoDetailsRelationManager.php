<?php

namespace App\Filament\Resources\LineStoResource\RelationManagers;

use App\Models\LineModelDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class LineStoDetailsRelationManager extends RelationManager
{
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
                    ->grow(true)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lineModelDetail.qad_number')
                    ->label('QAD - Part Details')
                    ->formatStateUsing(function ($record) {
                        $qad = $record->lineModelDetail?->qad_number ?? '';
                        $partName = $record->lineModelDetail?->part_name ?? '';
                        $partNumber = $record->lineModelDetail?->part_number ?? '';
                        
                        // Make QAD number bold
                        $qadFormatted = $qad ? '<strong>' . $qad . '</strong>' : '';
                        
                        return new \Illuminate\Support\HtmlString(
                            collect([$qadFormatted, $partName, $partNumber])
                                ->filter()
                                ->join('<br>')
                        );
                    })
                    ->searchable(['lineModelDetail.qad_number', 'lineModelDetail.part_name', 'lineModelDetail.part_number'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('total_on_hand')
                    ->label('OnHand')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        return $record->total_on_hand;
                    }),
                Tables\Columns\TextInputColumn::make('storage_count')
                    ->label('Storage')
                    ->type('number')
                    ->placeholder('-')
                    ->rules(['integer', 'min:0'])
                    // ->width('50px')
                    // ->extraAttributes(['style' => 'max-width: 20px', 'class' => 'max-w-20'])
                    ->extraInputAttributes([
                        'class' => 'text-center max-w-10',
                        'min' => '0',
                        'step' => '1',
                    ])
                    ->columnSpan(1)
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
                    // ->width('50px')
                    // ->extraAttributes(['style' => 'max-width: 20px', 'class' => 'max-w-20'])
                    ->extraInputAttributes([
                        'class' => 'text-center',
                        'min' => '0',
                        'step' => '1'
                    ])
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
                    ->width('50px')
                    ->extraInputAttributes([
                        'class' => 'text-center',
                        'min' => '0',
                        'step' => '1'
                    ])
                    ->afterStateUpdated(function ($state, $record) {
                        $record->update([
                            'ng_count' => (int) $state,
                            'total_count' => $record->storage_count + $record->wip_count + (int) $state
                        ]);
                    }),
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->storage_count + $record->wip_count + $record->ng_count),
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
                Tables\Filters\SelectFilter::make('lineModelDetail.model_id')
                    ->label('Model')
                    ->relationship('lineModelDetail', 'model_id')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('qad_number')
                    ->form([
                        Forms\Components\TextInput::make('qad_number')
                            ->label('QAD Number')
                            ->placeholder('Enter QAD Number')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['qad_number'],
                                fn(Builder $query, $qadNumber): Builder => $query->whereHas(
                                    'lineModelDetail',
                                    fn(Builder $query): Builder => $query->where('qad_number', 'like', "%{$qadNumber}%")
                                )
                            );
                    })
            ])
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
            ]);
        // ->defaultSort('lineModelDetail.model_id', 'asc');
    }
}
