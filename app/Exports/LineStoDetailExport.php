<?php

namespace App\Exports;

use App\Models\LineStoDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LineStoDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $periodId;
    protected $lineId;

    public function __construct($periodId = null, $lineId = null)
    {
        $this->periodId = $periodId;
        $this->lineId = $lineId;
    }

    public function collection()
    {
        $query = LineStoDetail::query()
            ->with([
                'periodSto',
                'lineSto.creator',
                'line',
                'lineModelDetail'
            ]);

        if ($this->periodId) {
            $query->where('period_id', $this->periodId);
        }

        if ($this->lineId) {
            $query->where('line_id', $this->lineId);
        }

        return $query->orderBy('period_id', 'asc')
                    ->orderBy('line_id', 'asc')
                    ->orderBy('line_model_detail_id', 'asc')
                    ->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'PERIODE STO',
            'TGL PERIODE DIBUAT',
            'PERIODE STATUS',
            'MULAI STO',
            'PROGRESS STO',
            'PIC',
            'LINE',
            'PROJECT',
            'QAD',
            'NAMA PART',
            'NO PART',
            'DESCRIPTION',
            'SUPPLIER',
            'STD PACKING',
            'STORAGE',
            'STATUS',
            'QTY STORAGE',
            'QTY WIP',
            'QTY NG',
            'TOTAL',
            'REMARK',
            'UPDATE'
        ];
    }

    public function map($lineStoDetail): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            $lineStoDetail->periodSto->period_sto ?? '',
            $lineStoDetail->periodSto->created_at ? $lineStoDetail->periodSto->created_at->format('d/m/Y') : '',
            $lineStoDetail->periodSto->status ?? '',
            $lineStoDetail->lineSto?->sto_start_at ? $lineStoDetail->lineSto->sto_start_at->format('d/m/Y H:i') : '',
            $lineStoDetail->lineSto?->progress ?? '',
            $lineStoDetail->lineSto?->creator?->name ?? '',
            $lineStoDetail->line->line ?? '',
            $lineStoDetail->lineModelDetail->model_id ?? '',
            $lineStoDetail->lineModelDetail->qad_number ?? '',
            $lineStoDetail->lineModelDetail->part_name ?? '',
            $lineStoDetail->lineModelDetail->part_number ?? '',
            $lineStoDetail->lineModelDetail->desc ?? '',
            $lineStoDetail->lineModelDetail->supplier ?? '',
            $lineStoDetail->lineModelDetail->std_packing ?? '',
            $lineStoDetail->lineModelDetail->storage ?? '',
            $lineStoDetail->lineModelDetail->type ?? '',
            $lineStoDetail->storage_count ?? '',
            $lineStoDetail->wip_count ?? '',
            $lineStoDetail->ng_count ?? '',
            $lineStoDetail->total_count ?? '',
            $lineStoDetail->remark ?? '',
            $lineStoDetail->updated_at ? $lineStoDetail->updated_at->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}