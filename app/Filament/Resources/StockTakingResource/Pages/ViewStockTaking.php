<?php

namespace App\Filament\Resources\StockTakingResource\Pages;

use App\Filament\Resources\StockTakingResource;
use App\Models\StockTakingDetail;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStockTaking extends ViewRecord
{
    protected static string $resource = StockTakingResource::class;
    
    protected static string $view = 'filament.resources.stock-taking-resource.pages.view';

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, $this->getViewData());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
            'chartData' => $this->getChartData(),
        ];
    }

    protected function getChartData(): array
    {
        $stockTakingDetails = StockTakingDetail::with('modelStructureDetail')
            ->where('stock_taking_id', $this->record->id)
            ->get();

        $chartData = [];
        $labels = [];

        foreach ($stockTakingDetails as $detail) {
            $qad = $detail->modelStructureDetail->qad ?? 'Unknown';
            $labels[] = $qad;
            
            $chartData[] = [
                'qad' => $qad,
                'part_number' => $detail->modelStructureDetail->part_number ?? '',
                'part_name' => $detail->modelStructureDetail->part_name ?? '',
                'storage_count' => $detail->getActualStorageCount(),
                'wip_count' => $detail->getActualWipCount(),
                'ng_count' => $detail->getActualNgCount(),
                'total_count' => $detail->getActualStorageCount() + $detail->getActualWipCount() + $detail->getActualNgCount(),
            ];
        }

        return [
            'labels' => $labels,
            'data' => $chartData,
        ];
    }
}