<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductStructure;

class ProductStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productStructures = [
            [
                'item_number' => 'MUF001',
                'category' => 'MUFFLER',
                'model' => 'AVANZA',
            ],
            [
                'item_number' => 'MUF002',
                'category' => 'MUFFLER',
                'model' => 'XENIA',
            ],
            [
                'item_number' => 'DS001',
                'category' => 'DOOR SASH',
                'model' => 'AVANZA',
            ],
            [
                'item_number' => 'DS002',
                'category' => 'DOOR SASH',
                'model' => 'XENIA',
            ],
            [
                'item_number' => 'CON001',
                'category' => 'CONVERTER',
                'model' => 'AVANZA',
            ],
            [
                'item_number' => 'CON002',
                'category' => 'CONVERTER',
                'model' => 'XENIA',
            ],
            [
                'item_number' => 'FRM001',
                'category' => 'FRAME',
                'model' => 'AVANZA',
            ],
            [
                'item_number' => 'FRM002',
                'category' => 'FRAME',
                'model' => 'XENIA',
            ],
        ];

        foreach ($productStructures as $structure) {
            ProductStructure::create($structure);
        }

        $this->command->info('ProductStructure seeded successfully with ' . count($productStructures) . ' records!');
    }
}