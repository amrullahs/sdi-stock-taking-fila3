<?php

namespace App\Livewire;

use App\Models\ModelStructureDetail;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class BomTableModal extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public string $modelName;

    public function mount(string $modelName): void
    {
        $this->modelName = $modelName;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ModelStructureDetail::query()->where('model', $this->modelName))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->size(60)
                    ->getStateUsing(function ($record) {
                        // Cek apakah field image ada dan tidak kosong
                        if (!empty($record->image)) {
                            return asset($record->image);
                        }
                        
                        // Fallback ke gambar berdasarkan QAD
                        $extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
                        foreach ($extensions as $ext) {
                            $imagePath = "storage/img/{$record->qad}.{$ext}";
                            if (file_exists(public_path($imagePath))) {
                                return asset($imagePath);
                            }
                        }
                        
                        // Default image jika tidak ditemukan
                        return url('/images/no-image.svg');
                    })
                    ->defaultImageUrl(url('/images/no-image.svg'))
                    ->toggleable(),
                TextColumn::make('qad')
                    ->label('QAD')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('desc1')
                    ->label('Description 1')
                    ->sortable()
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record): string => $record->desc1)
                    ->toggleable(),
                TextColumn::make('desc2')
                    ->label('Description 2')
                    ->sortable()
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record): string => $record->desc2)
                    ->toggleable(),
                TextColumn::make('supplier')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('suplier_code')
                    ->label('Supplier Code')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('standard_packing')
                    ->label('Standard Packing')
                    ->sortable()
                    ->searchable()
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('storage')
                    ->label('Storage')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('supplier')
                    ->label('Supplier')
                    ->options(function () {
                        return ModelStructureDetail::where('model', $this->modelName)
                            ->distinct()
                            ->pluck('supplier', 'supplier')
                            ->filter()
                            ->toArray();
                    })
                    ->searchable(),
                SelectFilter::make('storage')
                    ->label('Storage')
                    ->options(function () {
                        return ModelStructureDetail::where('model', $this->modelName)
                            ->distinct()
                            ->pluck('storage', 'storage')
                            ->filter()
                            ->toArray();
                    })
                    ->searchable(),
                Filter::make('standard_packing_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min_packing')
                            ->label('Min Standard Packing')
                            ->numeric(),
                        \Filament\Forms\Components\TextInput::make('max_packing')
                            ->label('Max Standard Packing')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_packing'],
                                fn (Builder $query, $value): Builder => $query->where('standard_packing', '>=', $value),
                            )
                            ->when(
                                $data['max_packing'],
                                fn (Builder $query, $value): Builder => $query->where('standard_packing', '<=', $value),
                            );
                    })
            ])
            ->actions([
                // Bisa ditambahkan action jika diperlukan
            ])
            ->bulkActions([
                // Bisa ditambahkan bulk action jika diperlukan
            ])
            ->defaultSort('id', 'asc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped()
            ->searchable()
            ->searchPlaceholder('Search BOM details...')
            ->emptyStateHeading('No BOM Details Found')
            ->emptyStateDescription('There are no BOM details available for this model.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public function render()
    {
        return view('livewire.bom-table-modal');
    }
}
