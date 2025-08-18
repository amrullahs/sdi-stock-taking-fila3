<?php

namespace App\Livewire;

use App\Models\ProductStructureDetail;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class BomTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $productStructureId;

    public function mount($productStructureId)
    {
        $this->productStructureId = $productStructureId;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductStructureDetail::query()->where('parent_item', $this->productStructureId))
            ->columns([
                TextColumn::make('component_item')
                    ->label('Component Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity_per')
                    ->label('Quantity Per')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label('Unit of Measure')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('operation')
                    ->label('Operation')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('scrap')
                    ->label('Scrap %')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : '-'),
                TextColumn::make('start_effective')
                    ->label('Start Effective')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('end_effective')
                    ->label('End Effective')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('remaks')
                    ->label('Remarks')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('unit_of_measure')
                    ->label('Unit of Measure')
                    ->options(function () {
                        return ProductStructureDetail::where('parent_item', $this->productStructureId)
                            ->distinct()
                            ->pluck('unit_of_measure', 'unit_of_measure')
                            ->filter()
                            ->sort()
                            ->toArray();
                    }),
                Filter::make('has_scrap')
                    ->label('Has Scrap')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('scrap')->where('scrap', '>', 0)),
            ])
            ->defaultSort('component_item')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public function render()
    {
        return view('livewire.bom-table');
    }
}