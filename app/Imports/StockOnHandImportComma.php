<?php

namespace App\Imports;

use App\Models\StockOnHand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Carbon\Carbon;

class StockOnHandImportComma implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    protected $periodStoId;
    
    public function __construct($periodStoId)
    {
        $this->periodStoId = $periodStoId;
    }
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Validasi: hanya item_number yang wajib diisi
        if (empty($row['item_number'])) {
            return null;
        }

        // Use updateOrCreate to prevent duplicates
        StockOnHand::updateOrCreate(
            [
                'item_number' => trim($row['item_number']),
                'period_sto_id' => $this->periodStoId,
            ],
            [
                'desc' => !empty($row['desc']) ? trim($row['desc']) : null,
                'location' => !empty($row['location']) ? trim($row['location']) : null,
                'lot' => !empty($row['lot']) ? trim($row['lot']) : null,
                'ref' => !empty($row['ref']) ? trim($row['ref']) : null,
                'status' => !empty($row['status']) ? trim($row['status']) : null,
                'qty_on_hand' => is_numeric($row['qty_on_hand'] ?? null) ? (int)$row['qty_on_hand'] : null,
                'confirming' => !empty($row['confirming']) ? trim($row['confirming']) : null,
                'created' => !empty($row['created']) ? Carbon::parse($row['created']) : null,
                'total_on_hand' => is_numeric($row['total_on_hand'] ?? null) ? (int)$row['total_on_hand'] : null,
                'uploaded' => now(),
            ]
        );
        
        return null; // Return null since we're handling the creation manually
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8'
        ];
    }
}