@extends('layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">MongoDB</h1>
        <div class="flex items-center gap-2 text-sm">
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">Project: {{ $server->project->name ?? 'N/A' }}</span>
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">Server: {{ $server->ip }}</span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: <span id="lastUpdated">{{ now()->format('M d, H:i') }}</span></div>
        <button id="refreshBtn" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200 transition">
            Refresh
        </button>
    </div>
</header>

<!-- Top Row: 4 Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Current Connections -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-[rgb(var(--color-primary)/.12)] flex items-center justify-center text-[rgb(var(--color-primary))]">
                <i class="ri-user-line"></i>
            </div>
            <span class="text-xl font-semibold text-gray-800" id="currentConnections">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Current Connections</div>
        <div class="text-xs text-gray-500">Active connections</div>
    </div>

    <!-- Queries / Sec -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-yellow-100 flex items-center justify-center text-yellow-600">
                <i class="ri-database-2-line"></i>
            </div>
            <span class="text-xl font-semibold text-gray-800" id="queriesPerSec">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Queries / Sec</div>
        <div class="text-xs text-gray-500">Query operations per second</div>
    </div>

    <!-- Memory Usage -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-red-100 flex items-center justify-center text-red-600">
                <i class="ri-computer-line"></i>
            </div>
            <span class="text-xl font-semibold text-gray-800" id="memoryUsage">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Memory Usage</div>
        <div class="text-xs text-gray-500">Resident memory (MB)</div>
    </div>

    <!-- Available Connections -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-500">
                <i class="ri-plug-line"></i>
            </div>
            <span class="text-xl font-semibold text-gray-800" id="availableConnections">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Available Connections</div>
        <div class="text-xs text-gray-500">Available connections</div>
    </div>
</div>

<!-- Second Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Operations Per Second -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-gray-700 font-medium">Operations Per Second</h3>
            <div class="text-xs text-gray-500" id="opsChartTime">Last 60 minutes</div>
        </div>
        <div id="mongoOpsPerSecond" class="h-64"></div>
    </div>

    <!-- MongoDB Connection Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">MongoDB Connection Usage</h3>
        </div>
        
        <div class="flex-1 flex flex-col items-center justify-center">
            <div id="mongoConnectionUsage" class="w-48 h-48"></div>
            <div class="w-full flex justify-between text-xs mt-6">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-[rgb(var(--color-primary))]"></span>
                    <div>
                        <div class="font-medium text-gray-700">Current Connections</div>
                        <div class="text-gray-500" id="currentConnectionsText">0</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-blue-500"></span>
                    <div>
                        <div class="font-medium text-gray-700">Available Connections</div>
                        <div class="text-gray-500" id="availableConnectionsText">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memory Usage Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-gray-700 font-medium">Memory Usage</h3>
            <div class="text-xs text-gray-500" id="memoryChartTime">Last 60 minutes</div>
        </div>
        <div id="mongoMemoryUsage" class="h-64"></div>
    </div>
</div>

<!-- Third Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- MongoDB Operations Overview -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">MongoDB Operations Overview</h3>
        </div>
        
        <div class="flex gap-1 mb-4">
            <span class="px-2 py-1 text-xs font-bold text-white bg-[rgb(var(--color-primary))] rounded-sm">QUERY</span>
            <span class="px-2 py-1 text-xs font-bold text-white bg-green-500 rounded-sm">INSERT</span>
            <span class="px-2 py-1 text-xs font-bold text-white bg-blue-500 rounded-sm">UPDATE</span>
            <span class="px-2 py-1 text-xs font-bold text-white bg-yellow-500 rounded-sm">DELETE</span>
            <span class="px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-sm">COMMAND</span>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <i class="ri-search-line text-gray-400"></i>
                    Query Operations
                </span>
                <span class="font-medium text-gray-800" id="totalQueries">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <i class="ri-add-line text-gray-400"></i>
                    Insert Operations
                </span>
                <span class="font-medium text-gray-800" id="totalInserts">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <i class="ri-refresh-line text-gray-400"></i>
                    Update Operations
                </span>
                <span class="font-medium text-gray-800" id="totalUpdates">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <i class="ri-delete-bin-line text-gray-400"></i>
                    Delete Operations
                </span>
                <span class="font-medium text-gray-800" id="totalDeletes">0</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 flex items-center gap-2">
                    <i class="ri-terminal-box-line text-gray-400"></i>
                    Command Operations
                </span>
                <span class="font-medium text-gray-800" id="totalCommands">0</span>
            </div>
        </div>
    </div>

    <!-- Network Traffic -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-gray-700 font-medium">Network Traffic</h3>
                <div class="text-xs text-gray-500 mt-1" id="networkTrafficTime">Last 60 minutes</div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600 mb-1">Bytes In</div>
                <div class="text-2xl font-bold text-gray-800" id="bytesIn">0 MB</div>
                <div class="text-xs text-gray-500">Total received</div>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600 mb-1">Bytes Out</div>
                <div class="text-2xl font-bold text-gray-800" id="bytesOut">0 MB</div>
                <div class="text-xs text-gray-500">Total sent</div>
            </div>
        </div>
        
        <div class="mt-6">
            <div id="networkTrafficChart" class="h-48"></div>
        </div>
    </div>
</div>

<!-- API Status -->
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-gray-700 font-medium">API Connection Status</h3>
        <span id="apiStatus" class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            Checking...
        </span>
    </div>
    <div class="text-sm text-gray-600" id="apiMessage">Checking connection to MongoDB API...</div>
</div>

@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>
<script>
    const serverId = {{ $server->id }};
    const serverIp = "{{ $server->ip }}";
    
    // Initialize charts with empty data
    let opsChart, memoryChart, connectionChart, networkChart;
    
    function initializeCharts() {
        // Operations Per Second Chart
        opsChart = new ApexCharts(document.querySelector('#mongoOpsPerSecond'), {
            series: [{
                name: 'Operations',
                data: []
            }],
            chart: {
                height: '100%',
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: ['#3B82F6'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            grid: { borderColor: '#e7e7e7', row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
            markers: { size: 4 },
            xaxis: {
                categories: [],
                labels: { style: { colors: '#6b7280', fontSize: '12px' } }
            },
            yaxis: {
                title: { text: 'Ops/Sec', style: { color: '#6b7280', fontSize: '12px' } },
                labels: { style: { colors: '#6b7280', fontSize: '12px' } }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' ops/sec';
                    }
                }
            }
        });
        opsChart.render();
        
        // Memory Usage Chart
        memoryChart = new ApexCharts(document.querySelector('#mongoMemoryUsage'), {
            series: [{
                name: 'Memory (MB)',
                data: []
            }],
            chart: {
                height: '100%',
                type: 'area',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: ['#10B981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            grid: { borderColor: '#e7e7e7', row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
            xaxis: {
                categories: [],
                labels: { style: { colors: '#6b7280', fontSize: '12px' } }
            },
            yaxis: {
                title: { text: 'MB', style: { color: '#6b7280', fontSize: '12px' } },
                labels: { style: { colors: '#6b7280', fontSize: '12px' } }
            }
        });
        memoryChart.render();
        
        // Connection Usage Gauge
        connectionChart = new ApexCharts(document.querySelector('#mongoConnectionUsage'), {
            series: [0],
            chart: {
                height: '100%',
                type: 'radialBar',
            },
            colors: ['#8B5CF6'],
            plotOptions: {
                radialBar: {
                    hollow: { size: '70%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            fontSize: '22px',
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    }
                }
            },
            labels: ['Connection Usage']
        });
        connectionChart.render();
        
        // Network Traffic Chart
        networkChart = new ApexCharts(document.querySelector('#networkTrafficChart'), {
            series: [
                { name: 'Bytes In', data: [] },
                { name: 'Bytes Out', data: [] }
            ],
            chart: {
                height: '100%',
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: ['#3B82F6', '#10B981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            grid: { borderColor: '#e7e7e7' },
            xaxis: {
                categories: [],
                labels: { style: { colors: '#6b7280', fontSize: '12px' } }
            },
            yaxis: {
                title: { text: 'MB', style: { color: '#6b7280', fontSize: '12px' } },
                labels: { style: { colors: '#6b7280', fontSize: '12px' } }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                fontSize: '12px',
                labels: { colors: '#6b7280' }
            }
        });
        networkChart.render();
    }
    
    // Fetch MongoDB data
    async function fetchMongoDBData() {
        try {
            const response = await fetch(`/admin/assets/mongodb/${serverId}/data`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Failed to fetch MongoDB data:', error);
            return null;
        }
    }
    
    // Update dashboard with data
    function updateDashboard(data) {
        if (!data || !data.ok) {
            // Show error state
            document.getElementById('apiStatus').className = 'px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
            document.getElementById('apiStatus').textContent = 'Disconnected';
            document.getElementById('apiMessage').textContent = data?.message || 'Failed to fetch MongoDB data';
            return;
        }
        
        // Update API status
        document.getElementById('apiStatus').className = 'px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
        document.getElementById('apiStatus').textContent = 'Connected';
        document.getElementById('apiMessage').textContent = 'Connected to MongoDB API successfully';
        
        // Update metrics
        const metrics = data.summary || {};
        
        // Top metrics
        document.getElementById('currentConnections').textContent = metrics.connections_current || '0';
        document.getElementById('queriesPerSec').textContent = metrics.queries_per_second || '0';
        document.getElementById('memoryUsage').textContent = metrics.memory_resident || '0';
        document.getElementById('availableConnections').textContent = metrics.connections_available || '0';
        
        // Connection usage
        document.getElementById('currentConnectionsText').textContent = metrics.connections_current || '0';
        document.getElementById('availableConnectionsText').textContent = metrics.connections_available || '0';
        
        // Operation totals
        document.getElementById('totalQueries').textContent = metrics.queries_per_second || '0';
        document.getElementById('totalInserts').textContent = metrics.inserts_per_second || '0';
        document.getElementById('totalUpdates').textContent = metrics.updates_per_second || '0';
        document.getElementById('totalDeletes').textContent = metrics.deletes_per_second || '0';
        document.getElementById('totalCommands').textContent = metrics.commands_per_second || '0';
        
        // Network traffic
        document.getElementById('bytesIn').textContent = (metrics.network_in || 0) + ' MB';
        document.getElementById('bytesOut').textContent = (metrics.network_out || 0) + ' MB';
        
        // Update charts if we have chart data
        if (data.chartData) {
            // Update operations chart
            if (data.chartData.operations && opsChart) {
                opsChart.updateSeries([{
                    name: 'Operations',
                    data: data.chartData.operations.data || []
                }]);
                opsChart.updateOptions({
                    xaxis: { categories: data.chartData.operations.labels || [] }
                });
            }
            
            // Update memory chart
            if (data.chartData.memory && memoryChart) {
                memoryChart.updateSeries([{
                    name: 'Memory (MB)',
                    data: data.chartData.memory.data || []
                }]);
                memoryChart.updateOptions({
                    xaxis: { categories: data.chartData.memory.labels || [] }
                });
            }
            
            // Update connection gauge
            if (metrics.connections_percent && connectionChart) {
                connectionChart.updateSeries([metrics.connections_percent]);
            }
            
            // Update network chart
            if (data.chartData.network && networkChart) {
                networkChart.updateSeries([
                    { name: 'Bytes In', data: data.chartData.network.in || [] },
                    { name: 'Bytes Out', data: data.chartData.network.out || [] }
                ]);
                networkChart.updateOptions({
                    xaxis: { categories: data.chartData.network.labels || [] }
                });
            }
        }
        
        // Update timestamp
        document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
    }
    
    // Refresh function
    async function refreshData() {
        const refreshBtn = document.getElementById('refreshBtn');
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="ri-refresh-line animate-spin"></i> Refreshing...';
        
        try {
            const data = await fetchMongoDBData();
            updateDashboard(data);
        } catch (error) {
            console.error('Refresh failed:', error);
        } finally {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = 'Refresh';
        }
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        refreshData();
        
        // Set up refresh button
        document.getElementById('refreshBtn').addEventListener('click', refreshData);
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
    });
</script>
@endpush