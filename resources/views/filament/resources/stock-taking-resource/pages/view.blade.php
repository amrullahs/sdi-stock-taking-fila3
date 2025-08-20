<x-filament-panels::page>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 min-h-screen">
        <!-- Left Column: Stock Taking Information -->
        <div class="space-y-6">
            <!-- Stock Taking Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5 inline mr-2" />
                    Stock Taking Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period STO</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->periodSto->period_sto ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Model Structure</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->modelStructure->model ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Model</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->model }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($record->status === 'open') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @elseif($record->status === 'on_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @endif">
                            {{ \App\Models\StockTaking::getStatuses()[$record->status] ?? $record->status }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Progress</label>
                        <div class="mt-1 flex items-center">
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $record->progress }}%"></div>
                            </div>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $record->progress }}%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">STO User</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->sto_user ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tracking Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <x-heroicon-o-clock class="w-5 h-5 inline mr-2" />
                    Tracking Information
                </h3>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $record->sto_start_at ? $record->sto_start_at->format('d M Y H:i:s') : 'Not started yet' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Update</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $record->sto_update_at ? $record->sto_update_at->format('d M Y H:i:s') : 'No updates yet' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submit Time</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $record->sto_submit_at ? $record->sto_submit_at->format('d M Y H:i:s') : 'Not submitted yet' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created At</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->created_at->format('d M Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Updated At</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->updated_at->format('d M Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Chart -->
        <div class="space-y-6">
            <!-- Chart Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <x-heroicon-o-chart-bar class="w-5 h-5 inline mr-2" />
                    Stock Count by QAD
                </h3>

                <div class="mb-4">
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span class="text-gray-700 dark:text-gray-300">Storage Count</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                            <span class="text-gray-700 dark:text-gray-300">WIP Count</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span class="text-gray-700 dark:text-gray-300">NG Count</span>
                        </div>
                    </div>
                </div>

                <div class="relative w-full" style="height: 400px; min-height: 300px;">
                    <canvas id="stockChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <x-heroicon-o-calculator class="w-5 h-5 inline mr-2" />
                    Summary
                </h3>

                @php
                    $totalStorage = collect($chartData['data'])->sum('storage_count');
                    $totalWip = collect($chartData['data'])->sum('wip_count');
                    $totalNg = collect($chartData['data'])->sum('ng_count');
                    $grandTotal = $totalStorage + $totalWip + $totalNg;
                    $totalItems = count($chartData['data']);
                @endphp

                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalStorage) }}</div>
                        <div class="text-sm text-green-700 dark:text-green-300">Storage Count</div>
                    </div>

                    <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($totalWip) }}</div>
                        <div class="text-sm text-yellow-700 dark:text-yellow-300">WIP Count</div>
                    </div>

                    <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($totalNg) }}</div>
                        <div class="text-sm text-red-700 dark:text-red-300">NG Count</div>
                    </div>

                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($grandTotal) }}</div>
                        <div class="text-sm text-blue-700 dark:text-blue-300">Total Count</div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalItems }} Items</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total QAD Items</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('stockChart').getContext('2d');
            const chartData = @json($chartData);

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Storage Count',
                            data: chartData.data.map(item => item.storage_count),
                            backgroundColor: '#10b981', // green-500
                            borderColor: '#059669', // green-600
                            borderWidth: 1
                        },
                        {
                            label: 'WIP Count',
                            data: chartData.data.map(item => item.wip_count),
                            backgroundColor: '#f59e0b', // yellow-500
                            borderColor: '#d97706', // yellow-600
                            borderWidth: 1
                        },
                        {
                            label: 'NG Count',
                            data: chartData.data.map(item => item.ng_count),
                            backgroundColor: '#ef4444', // red-500
                            borderColor: '#dc2626', // red-600
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'QAD'
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const item = chartData.data[index];
                                    return `QAD: ${item.qad}`;
                                },
                                afterTitle: function(context) {
                                    const index = context[0].dataIndex;
                                    const item = chartData.data[index];
                                    return [
                                        `Part Number: ${item.part_number}`,
                                        `Part Name: ${item.part_name}`
                                    ];
                                },
                                footer: function(context) {
                                    const index = context[0].dataIndex;
                                    const item = chartData.data[index];
                                    return `Total: ${item.total_count}`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
