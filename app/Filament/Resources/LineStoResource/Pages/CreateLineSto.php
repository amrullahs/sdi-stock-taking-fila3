<?php

namespace App\Filament\Resources\LineStoResource\Pages;

use App\Filament\Resources\LineStoResource;
use App\Models\LineSto;
use App\Models\Line;
use App\Models\PeriodSto;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLineSto extends CreateRecord
{
    protected static string $resource = LineStoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi kombinasi period_id dan line_id yang unik
        $existingLineSto = LineSto::where('period_id', $data['period_id'])
            ->where('line_id', $data['line_id'])
            ->first();

        if ($existingLineSto) {
            $line = Line::find($data['line_id']);
            $period = PeriodSto::find($data['period_id']);

            $lineName = $line ? $line->line : 'Unknown Line';
            $periodInfo = $period ? "{$period->period_sto} - {$period->site}" : 'Unknown Period';

            Notification::make()
                ->title("Sudah ada Line STO untuk {$lineName}, pada Periode {$periodInfo}. Silahkan pilih Line lain.")
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
