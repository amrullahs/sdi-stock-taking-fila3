<div class="space-y-6">
    {{-- Header dengan informasi summary --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-4">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @php
                $summary = $this->getSummaryData();
            @endphp
            
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($summary['total_storage']) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Storage</div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ number_format($summary['total_wip']) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">WIP</div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ number_format($summary['total_ng']) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">NG</div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ number_format($summary['grand_total']) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                    {{ number_format($summary['total_items']) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Items</div>
            </div>
        </div>
    </div>

    {{-- Info banner --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>Tip:</strong> Click on Storage Count, WIP Count, or NG Count cells to edit values directly. Total Count will be calculated automatically.
            </p>
        </div>
    </div>

    {{-- Filament Table --}}
    <div class="filament-table-container">
        {{ $this->table }}
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh summary when table data changes
    document.addEventListener('livewire:updated', function () {
        // Optional: Add any custom JavaScript for enhanced UX
        console.log('Stock Taking Detail table updated');
    });
</script>
@endpush