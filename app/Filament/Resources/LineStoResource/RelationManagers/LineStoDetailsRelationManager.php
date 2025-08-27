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
                    ->label('QAD')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('lineModelDetail.part_name')
                    ->label('Part Name')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('lineModelDetail.part_number')
                    ->label('Part Nbr')
                    ->sortable()
                    ->searchable()
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
                    ->view('filament.tables.columns.number-input-with-buttons')
                    ->viewData(['columnName' => 'storage_count'])
                    ->width('100px')
                    ->extraAttributes(['class' => 'text-center']),
                Tables\Columns\TextInputColumn::make('wip_count')
                    ->label('WIP')
                    ->view('filament.tables.columns.number-input-with-buttons')
                    ->viewData(['columnName' => 'wip_count'])
                    ->width('120px')
                    ->extraAttributes(['class' => 'text-center']),
                Tables\Columns\TextInputColumn::make('ng_count')
                    ->label('NG')
                    ->view('filament.tables.columns.number-input-with-buttons')
                    ->viewData(['columnName' => 'ng_count'])
                    ->width('120px')
                    ->extraAttributes(['class' => 'text-center']),
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->storage_count + $record->wip_count + $record->ng_count),
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

    /**
     * Update count field via AJAX
     */
    public function updateCount($recordId, $field, $value)
    {
        try {
            $record = $this->getRelationship()->find($recordId);

            if (!$record) {
                return [
                    'success' => false,
                    'message' => 'Record not found'
                ];
            }

            // Validate field name
            if (!in_array($field, ['storage_count', 'wip_count', 'ng_count'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid field name'
                ];
            }

            // Validate value
            $value = $value === null || $value === '' ? null : (int) $value;
            if ($value !== null && $value < 0) {
                return [
                    'success' => false,
                    'message' => 'Value cannot be negative'
                ];
            }

            // Update the field
            $record->update([$field => $value]);

            return [
                'success' => true,
                'message' => 'Count updated successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
}
