<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ModelStructure;
use App\Models\ModelStructureDetail;

class ModelStructureDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all model structures
        $modelStructures = ModelStructure::all();
        
        foreach ($modelStructures as $modelStructure) {
            // Create 3-5 detail records for each model structure
            $detailCount = rand(3, 5);
            
            for ($i = 1; $i <= $detailCount; $i++) {
                ModelStructureDetail::create([
                    'model_structure_id' => $modelStructure->id,
                    'model' => $modelStructure->model,
                    'qad' => 'QAD' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'desc1' => 'Part Description ' . $i . ' for ' . $modelStructure->model,
                    'desc2' => 'Additional Description ' . $i,
                    'supplier' => 'Supplier ' . chr(64 + $i), // A, B, C, etc.
                    'suplier_code' => 'SUP' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'standard_packing' => rand(10, 100),
                    'storage' => 'Storage-' . chr(64 + $i),
                ]);
            }
        }
        
        $this->command->info('ModelStructureDetail seeded successfully!');
    }
}