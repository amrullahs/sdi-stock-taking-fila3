<?php

namespace App\Filament\Resources\LineStoDetailResource\Pages;

use App\Filament\Resources\LineStoDetailResource;
use App\Exports\LineStoDetailExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListLineStoDetails extends ListRecords
{
    protected static string $resource = LineStoDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Export button dihilangkan dari halaman Line STO Detail
            // Tombol export sekarang hanya tersedia di modal Period STO
        ];
    }
}
