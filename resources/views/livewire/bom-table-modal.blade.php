<div class="p-6">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            BOM Details for Model: {{ $modelName }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Interactive table with sorting, search, filters, column management, and pagination
        </p>
    </div>

    <div class="filament-table-container">
        {{ $this->table }}
    </div>
</div>
