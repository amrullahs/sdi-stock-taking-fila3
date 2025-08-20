<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ModelStructure;
use App\Models\ProductStructure;

class ModelStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data model distinct dari index_product_structure
        $distinctModels = DB::table('index_product_structure')
            ->select('model')
            ->whereNotNull('model')
            ->where('model', '!=', '')
            ->distinct()
            ->get();

        // Insert ke tabel model_structure
        foreach ($distinctModels as $modelData) {
            ModelStructure::updateOrCreate(
                ['model' => $modelData->model],
                [
                    'model' => $modelData->model,
                    'line' => 'Line A', // Default line, bisa diubah manual nanti
                ]
            );
        }

        $this->command->info('Model structure seeded with ' . $distinctModels->count() . ' distinct models.');
    }
}
