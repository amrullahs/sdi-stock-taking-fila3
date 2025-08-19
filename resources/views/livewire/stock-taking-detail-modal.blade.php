<!-- Container -->
<div class="container mx-auto p-6">


    {{-- Info banner --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-6">
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
    <div class="filament-table-container mt-6">
        {{ $this->table }}
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let stockChart = null;
    let resizeObserver = null;
    let retryCount = 0;
    const maxRetries = 15;
    let chartInitialized = false;

    function isCanvasVisible(canvas) {
        if (!canvas) return false;

        const rect = canvas.getBoundingClientRect();
        const computedStyle = window.getComputedStyle(canvas);

        const isVisible = rect.width > 0 &&
                         rect.height > 0 &&
                         computedStyle.display !== 'none' &&
                         computedStyle.visibility !== 'hidden' &&
                         computedStyle.opacity !== '0';

        console.log('Canvas visibility check:', {
            width: rect.width,
            height: rect.height,
            display: computedStyle.display,
            visibility: computedStyle.visibility,
            opacity: computedStyle.opacity,
            isVisible: isVisible
        });

        return isVisible;
    }

    function debugChartData() {
        const chartData = @json($this->getChartData());
        console.log('Chart data debug:', {
            hasData: !!chartData,
            hasLabels: !!(chartData && chartData.labels),
            labelsCount: chartData && chartData.labels ? chartData.labels.length : 0,
            hasDatasets: !!(chartData && chartData.data),
            storageCount: chartData && chartData.data ? chartData.data.storage_count : null,
            wipCount: chartData && chartData.data ? chartData.data.wip_count : null,
            ngCount: chartData && chartData.data ? chartData.data.ng_count : null
        });
        return chartData;
    }

    function initializeChart() {
        console.log('Attempting to initialize modal chart... (Attempt:', retryCount + 1, ')');

        const ctx = document.getElementById('stockChartModal');
        if (!ctx) {
            console.error('Modal canvas element not found');
            return;
        }

        // Check if canvas is visible and has proper dimensions
        if (!isCanvasVisible(ctx)) {
            console.log('Modal canvas not visible yet, will retry...');
            retryCount++;

            if (retryCount < maxRetries) {
                setTimeout(() => {
                    initializeChart();
                }, 300); // Retry after 300ms
            } else {
                console.error('Max retries reached, modal canvas still not visible');
            }
            return;
        }

        // Reset retry count on successful visibility check
        retryCount = 0;

        // Destroy existing chart if it exists
        if (stockChart) {
            stockChart.destroy();
            stockChart = null;
        }

        try {
            const chartData = @json($this->getChartData());
            console.log('Modal chart data:', chartData);

            if (!chartData || !chartData.labels || !chartData.data) {
                console.error('Invalid modal chart data');
                return;
            }

            stockChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Storage Count',
                            data: chartData.data.storage_count,
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1
                        },
                        {
                            label: 'WIP Count',
                            data: chartData.data.wip_count,
                            backgroundColor: '#f59e0b',
                            borderColor: '#d97706',
                            borderWidth: 1
                        },
                        {
                            label: 'NG Count',
                            data: chartData.data.ng_count,
                            backgroundColor: '#ef4444',
                            borderColor: '#dc2626',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: false,
                            title: {
                                display: true,
                                text: 'QAD'
                            }
                        },
                        y: {
                            stacked: false,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const qad = chartData.data.qad[index];
                                    return `QAD: ${qad}`;
                                },
                                afterTitle: function(context) {
                                    const index = context[0].dataIndex;
                                    const partNumber = chartData.data.part_number[index];
                                    const partName = chartData.data.part_name[index];
                                    return [
                                        `Part Number: ${partNumber}`,
                                        `Part Name: ${partName}`
                                    ];
                                },
                                footer: function(context) {
                                    const index = context[0].dataIndex;
                                    const total = chartData.data.total_count[index];
                                    return `Total: ${total}`;
                                }
                            }
                        }
                    }
                }
            });

            chartInitialized = true;
            console.log('Modal chart initialized successfully');

            // Setup ResizeObserver after chart init to handle container size changes
            const canvas = document.getElementById('stockChartModal');
            const container = canvas ? canvas.parentElement : null;
            if (container && 'ResizeObserver' in window) {
                if (resizeObserver) {
                    resizeObserver.disconnect();
                }
                resizeObserver = new ResizeObserver(entries => {
                    for (let entry of entries) {
                        const cr = entry.contentRect;
                        // If the container becomes visible and has size, update chart
                        if (cr.width > 0 && cr.height > 0 && stockChart) {
                            console.log('ResizeObserver: modal container size changed ->', cr.width, cr.height);
                            stockChart.resize();
                        }
                    }
                });
                resizeObserver.observe(container);
            }
        } catch (error) {
            console.error('Error initializing modal chart:', error);
        }
    }

    // Use MutationObserver to detect when canvas becomes visible
    function setupChartObserver() {
        const targetNode = document.body;
        const config = { childList: true, subtree: true, attributes: true, attributeFilter: ['style', 'class'] };

        const callback = function(mutationsList, observer) {
            const canvas = document.getElementById('stockChartModal');
            if (canvas && isCanvasVisible(canvas) && !chartInitialized) {
                console.log('Modal canvas detected as visible, initializing chart...');
                initializeChart();
            }
        };

        const observer = new MutationObserver(callback);
        observer.observe(targetNode, config);

        // Also try immediate initialization
        setTimeout(() => {
            initializeChart();
        }, 100);
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up modal chart observer...');
        setupChartObserver();
    });

    // Auto-refresh chart when table data changes
    document.addEventListener('livewire:updated', function() {
        console.log('Livewire updated, refreshing modal chart...');
        chartInitialized = false;
        retryCount = 0;
        setTimeout(() => {
            initializeChart();
        }, 300); // Give Livewire some time to update DOM
    });

    // Re-init on window resize if needed (especially when modal animates)
    window.addEventListener('resize', () => {
        if (stockChart) {
            stockChart.resize();
        } else if (!chartInitialized) {
            initializeChart();
        }
    });

    // Handle potential Alpine.js modal events
    document.addEventListener('alpine:init', () => {
        console.log('Alpine initialized, checking for modal chart...');
        setTimeout(() => {
            if (!chartInitialized) {
                initializeChart();
            }
        }, 500);
    });
</script>
@endpush
