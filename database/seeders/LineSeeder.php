<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Line;

class LineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lines = [
            [
                'line' => 'Line A',
                'leader' => 'John Doe',
            ],
            [
                'line' => 'Line B',
                'leader' => 'Jane Smith',
            ],
            [
                'line' => 'Line C',
                'leader' => 'Bob Johnson',
            ],
            [
                'line' => 'Line D',
                'leader' => 'Alice Brown',
            ],
            [
                'line' => 'Line E',
                'leader' => 'Charlie Wilson',
            ],
        ];

        foreach ($lines as $line) {
            Line::create($line);
        }

        $this->command->info('Line seeded successfully with ' . count($lines) . ' records!');
    }
}