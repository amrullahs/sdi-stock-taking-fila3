<?php

namespace App\Imports;

use App\Models\LineModelDetail;
use App\Models\Line;
use App\Models\ModelStructure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;

class LineModelDetailImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find line by name
        $line = Line::where('line', $row['line'])->first();
        if (!$line) {
            throw new \Exception("Line '{$row['line']}' not found");
        }

        // Find model by name (using PROJECT column)
        $model = ModelStructure::where('model', $row['project'])->first();
        if (!$model) {
            throw new \Exception("Project/Model '{$row['project']}' not found");
        }

        // Generate image filename from QAD value with .png extension
        $imageName = null;
        if (!empty($row['qad'])) {
            $imageName = $row['qad'] . '.png';
        }

        return new LineModelDetail([
            'line_id' => $line->id,
            'model_id' => $model->id,
            'qad_number' => $row['qad'] ?? null,
            'part_name' => $row['nama_part'] ?? null,
            'part_number' => $row['no_part'] ?? null,
            'desc' => $row['description'] ?? null,
            'supplier' => $row['supplier'] ?? null,
            'std_packing' => $row['std_packing'] ?? null,
            'storage' => $row['storage'] ?? null,
            'image' => $imageName,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'line' => 'required|string',
            'project' => 'required|string',
            'qad' => 'nullable|string|max:255',
            'nama_part' => 'nullable|string|max:255',
            'no_part' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'std_packing' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Custom attributes for validation error messages
     */
    public function customValidationAttributes()
    {
        return [
            'line' => 'Line',
            'project' => 'Project',
            'qad' => 'QAD',
            'nama_part' => 'Nama Part',
            'no_part' => 'No Part',
            'description' => 'Description',
            'supplier' => 'Supplier',
            'std_packing' => 'Standard Packing',
            'storage' => 'Storage',
        ];
    }
}