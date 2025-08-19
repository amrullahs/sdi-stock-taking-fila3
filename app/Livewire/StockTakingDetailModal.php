<?php

namespace App\Livewire;

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

    public function mount($stockTakingId, $modelName, $tanggalSto)
    {
        $this->stockTakingId = $stockTakingId;
        $this->modelName = $modelName;
        $this->tanggalSto = $tanggalSto;
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
                    ->width(120)
                    ->height(80)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; width: 100%; height: 100%;'
                    ])
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
                TextInputColumn::make('storage_count')
                    ->label('Storage Count')
                    ->type('number')
                    ->rules(['required', 'integer', 'min:0'])
                    ->afterStateUpdated(function ($record, $state) {
                        $record->update([
                            'storage_count' => (int) $state,
                            'total_count' => (int) $state + $record->wip_count + $record->ng_count
                        ]);
                        
                        Notification::make()
                            ->title('Storage count updated successfully')
                            ->success()
                            ->send();
                    }),
                TextInputColumn::make('wip_count')
                    ->label('WIP Count')
                    ->type('number')
                    ->rules(['required', 'integer', 'min:0'])
                    ->afterStateUpdated(function ($record, $state) {
                        $record->update([
                            'wip_count' => (int) $state,
                            'total_count' => $record->storage_count + (int) $state + $record->ng_count
                        ]);
                        
                        Notification::make()
                            ->title('WIP count updated successfully')
                            ->success()
                            ->send();
                    }),
                TextInputColumn::make('ng_count')
                    ->label('NG Count')
                    ->type('number')
                    ->rules(['required', 'integer', 'min:0'])
                    ->afterStateUpdated(function ($record, $state) {
                        $record->update([
                            'ng_count' => (int) $state,
                            'total_count' => $record->storage_count + $record->wip_count + (int) $state
                        ]);
                        
                        Notification::make()
                            ->title('NG count updated successfully')
                            ->success()
                            ->send();
                    }),
                TextColumn::make('total_count')
                    ->label('Total Count')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($record) => number_format($record->storage_count + $record->wip_count + $record->ng_count)),
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
                            fn (Builder $query): Builder => $query->where('storage_count', '>', 0),
                        )->when(
                            $data['value'] === '0',
                            fn (Builder $query): Builder => $query->where('storage_count', '=', 0),
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
                            fn (Builder $query): Builder => $query->where('wip_count', '>', 0),
                        )->when(
                            $data['value'] === '0',
                            fn (Builder $query): Builder => $query->where('wip_count', '=', 0),
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
                            fn (Builder $query): Builder => $query->where('ng_count', '>', 0),
                        )->when(
                            $data['value'] === '0',
                            fn (Builder $query): Builder => $query->where('ng_count', '=', 0),
                        );
                    }),
            ])
            ->actions([
                //
            ])
            ->defaultSort('modelStructureDetail.qad')
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
}