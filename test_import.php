<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PeriodSto;
use App\Models\StockOnHand;
use App\Imports\StockOnHandImport;
use Maatwebsite\Excel\Facades\Excel;

try {
    // Create new period
    $period = new PeriodSto();
    $period->period_sto = '2025-08-22';
    $period->created_by = 'Test User';
    $period->site = '7000';
    $period->save();
    
    echo "Period created with ID: {$period->id}\n";
    
    // Import data
    $import = new StockOnHandImport($period->id);
    Excel::import($import, storage_path('app/sample_stock_data.csv'));
    
    echo "Import completed\n";
    
    // Count records
    $count = StockOnHand::where('period_sto_id', $period->id)->count();
    echo "Total records imported: {$count}\n";
    
    // Show sample data
    $samples = StockOnHand::where('period_sto_id', $period->id)->take(3)->get();
    foreach ($samples as $sample) {
        echo "- {$sample->item_number}: {$sample->desc} (Qty: {$sample->qty_on_hand})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}