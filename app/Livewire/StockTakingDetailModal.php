<?php

namespace App\Livewire;

use App\Models\StockTaking;
use App\Models\StockTakingDetail;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Notifications\Notification;

class StockTakingDetailModal extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $stockTakingId;
    public $modelName;
    public $tanggalSto;
    public $stockTaking;

    public function mount($stockTakingId, $modelName, $tanggalSto)
    {
        $this->stockTakingId = $stockTakingId;
        $this->modelName = $modelName;
        $this->tanggalSto = $tanggalSto;
        $this->stockTaking = StockTaking::find($stockTakingId);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StockTakingDetail::query()
                    ->with('modelStructureDetail')
                    ->where('stock_taking_id', $this->stockTakingId)
            )
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('modelStructureDetail.image_url')
                    ->label('Image')
                    ->circular(false)
                    ->square(false)
                    ->width(180)
                    ->height(120)
                    ->extraAttributes(['class' => 'p-0 m-0'])
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; width: 100%; height: 100%;'
                    ])
                    ->getStateUsing(function ($record): ?string {
                        $modelStructureDetail = $record->modelStructureDetail;
                        if (!$modelStructureDetail) {
                            return null;
                        }

                        // Jika field image tidak null, gunakan nilai aslinya
                        if (!empty($modelStructureDetail->image)) {
                            return $modelStructureDetail->image;
                        }

                        // Jika image null, cari file berdasarkan nilai qad
                        if (!empty($modelStructureDetail->qad)) {
                            $extensions = ['png', 'jpg', 'jpeg', 'svg'];
                            foreach ($extensions as $ext) {
                                $imagePath = storage_path("app/public/img/{$modelStructureDetail->qad}.{$ext}");
                                if (file_exists($imagePath)) {
                                    return asset("storage/img/{$modelStructureDetail->qad}.{$ext}");
                                }
                            }
                        }

                        // Jika tidak ada file yang ditemukan, return null untuk fallback ke defaultImageUrl
                        return null;
                    })
                    ->defaultImageUrl('/images/no-image.svg'),
                TextColumn::make('modelStructureDetail.part_number')
                    ->label('Part Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('modelStructureDetail.part_name')
                    ->label('Part Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                \Filament\Tables\Columns\ViewColumn::make('storage_count')
                    ->label('Storage Count')
                    ->view('filament.tables.columns.number-input-with-buttons')
                    ->viewData(['columnName' => 'storage_count'])
                    ->width('120px')
                    ->extraAttributes(['class' => 'text-center']),
                \Filament\Tables\Columns\ViewColumn::make('wip_count')
                    ->label('WIP Count')
                    ->view('filament.tables.columns.number-input-with-buttons')
                    ->viewData(['columnName' => 'wip_count'])
                    ->width('120px')
                    ->extraAttributes(['class' => 'text-center']),
                \Filament\Tables\Columns\ViewColumn::make('ng_count')
                    ->label('NG Count')
                    ->view('filament.tables.columns.number-input-with-buttons')
                    ->viewData(['columnName' => 'ng_count'])
                    ->width('120px')
                    ->extraAttributes(['class' => 'text-center']),
                TextColumn::make('total_count')
                    ->label('Total Count')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn($record) => number_format($record->storage_count + $record->wip_count + $record->ng_count)),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('has_storage')
                    ->label('Has Storage Count')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === '1',
                            fn(Builder $query): Builder => $query->where('storage_count', '>', 0),
                        )->when(
                            $data['value'] === '0',
                            fn(Builder $query): Builder => $query->where('storage_count', '=', 0),
                        );
                    }),
                SelectFilter::make('has_wip')
                    ->label('Has WIP Count')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === '1',
                            fn(Builder $query): Builder => $query->where('wip_count', '>', 0),
                        )->when(
                            $data['value'] === '0',
                            fn(Builder $query): Builder => $query->where('wip_count', '=', 0),
                        );
                    }),
                SelectFilter::make('has_ng')
                    ->label('Has NG Count')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === '1',
                            fn(Builder $query): Builder => $query->where('ng_count', '>', 0),
                        )->when(
                            $data['value'] === '0',
                            fn(Builder $query): Builder => $query->where('ng_count', '=', 0),
                        );
                    }),
            ])
            ->actions([
                //
            ])
            ->defaultSort('id')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    public function render()
    {
        return view('livewire.stock-taking-detail-modal');
    }

    public function getSummaryData()
    {
        $details = StockTakingDetail::where('stock_taking_id', $this->stockTakingId)->get();

        return [
            'total_storage' => $details->sum('storage_count'),
            'total_wip' => $details->sum('wip_count'),
            'total_ng' => $details->sum('ng_count'),
            'grand_total' => $details->sum('storage_count') + $details->sum('wip_count') + $details->sum('ng_count'),
            'total_items' => $details->count(),
        ];
    }

    public function getChartData()
    {
        $details = StockTakingDetail::with('modelStructureDetail')
            ->where('stock_taking_id', $this->stockTakingId)
            ->get();

        $labels = [];
        $storageData = [];
        $wipData = [];
        $ngData = [];
        $totalData = [];
        $qadData = [];
        $partNumberData = [];
        $partNameData = [];

        foreach ($details as $detail) {
            $qad = $detail->modelStructureDetail->qad ?? 'Unknown';
            $partNumber = $detail->modelStructureDetail->part_number ?? 'Unknown';
            $partName = $detail->modelStructureDetail->part_name ?? 'Unknown';
            
            $labels[] = $qad;
            $storageData[] = $detail->storage_count ?? 0;
            $wipData[] = $detail->wip_count ?? 0;
            $ngData[] = $detail->ng_count ?? 0;
            $totalData[] = ($detail->storage_count ?? 0) + ($detail->wip_count ?? 0) + ($detail->ng_count ?? 0);
            $qadData[] = $qad;
            $partNumberData[] = $partNumber;
            $partNameData[] = $partName;
        }

        return [
            'labels' => $labels,
            'data' => [
                'storage_count' => $storageData,
                'wip_count' => $wipData,
                'ng_count' => $ngData,
                'total_count' => $totalData,
                'qad' => $qadData,
                'part_number' => $partNumberData,
                'part_name' => $partNameData,
            ]
        ];
    }

    public function updateCount($recordId, $field, $value)
    {
        $detail = StockTakingDetail::find($recordId);
        
        if ($detail) {
            $stockTaking = $detail->stockTaking;
            
            // Check if this is the first update for the entire stock taking
            $isFirstUpdate = false;
            if ($stockTaking && $stockTaking->isFirstUpdate()) {
                $isFirstUpdate = true;
            }
            
            $detail->$field = $value;
            
            // Recalculate total_count
            $detail->total_count = ($detail->storage_count ?? 0) + 
                                 ($detail->wip_count ?? 0) + 
                                 ($detail->ng_count ?? 0);
            
            $detail->save();
            
            // Update timestamps and progress for the parent StockTaking
            if ($stockTaking) {
                // Set start time if this is the first update
                if ($isFirstUpdate) {
                    $stockTaking->setStartTimeIfNotSet();
                }
                
                // Always update the update time
                $stockTaking->updateTimestamp();
                
                // Update progress
                $stockTaking->updateProgress();
            }
        }
    }
}
