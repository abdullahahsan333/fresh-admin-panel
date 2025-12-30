@extends('layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border border-gray-100 sticky top-0 z-10 mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-800">MySQL Monitoring</h1>
            <div id="apiStatusDot" class="hidden">
                <span class="inline-block w-2 h-2 rounded-full"></span>
            </div>
            <p class="text-gray-600">Real-time database performance metrics</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500" id="lastUpdated">
                Last updated: {{ now()->format('M d, H:i') }}
            </div>
            <button onclick="fetchMySQLData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- API Connection Status removed -->

    <!-- Top Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Current Connections -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.553 0-1 .447-1 1v4c0 .553.447 1 1 1h2c.553 0 1-.447 1-1v-4c0-.553-.447-1-1-1h-2z" />
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Usage</div>
                    <div class="text-lg font-semibold text-gray-800" id="connectionsUsage">--%</div>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1" id="currentConnections">--</h3>
            <p class="text-sm text-gray-600">Current Connections</p>
        </div>

        <!-- Queries Per Second -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Peak</div>
                    <div class="text-lg font-semibold text-gray-800" id="queriesPeak">--</div>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1" id="queriesPerSecond">--</h3>
            <p class="text-sm text-gray-600">Queries / Second</p>
        </div>

        <!-- Slow Queries -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Last Hour</div>
                    <div class="text-lg font-semibold text-gray-800" id="slowQueriesHour">--</div>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1" id="slowQueries">--</h3>
            <p class="text-sm text-gray-600">Slow Queries</p>
        </div>

        <!-- Threads Running -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Active</div>
                    <div class="text-lg font-semibold text-gray-800" id="threadsRunningCount">--</div>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1" id="threadsRunning">--</h3>
            <p class="text-sm text-gray-600">Threads Running</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Queries Per Second Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Queries Per Second</h3>
                <div class="text-sm text-gray-600" id="qpsLatest">Latest: --</div>
            </div>
            <div class="h-64">
                <canvas id="queriesChart"></canvas>
            </div>
        </div>

        <!-- Connection Usage -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Connection Usage</h3>
                <div class="text-sm text-gray-600" id="connectionStats">Max: --</div>
            </div>
            <div class="h-64">
                <canvas id="connectionsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Buffer Pool Hit Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">InnoDB Buffer Pool</h3>
                <div class="text-sm font-semibold" id="bufferPoolHitRate">--%</div>
            </div>
            <div class="h-48 mb-4">
                <canvas id="bufferPoolChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <div class="text-sm text-gray-600">Reads</div>
                    <div class="text-lg font-semibold" id="bufferPoolReads">--</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Requests</div>
                    <div class="text-lg font-semibold" id="bufferPoolRequests">--</div>
                </div>
            </div>
        </div>

        <!-- Query Type Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Query Types</h3>
                <div class="text-sm text-gray-600">Distribution</div>
            </div>
            <div class="h-48 mb-4">
                <canvas id="queryTypesChart"></canvas>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        <span class="text-sm">SELECT</span>
                    </div>
                    <span class="text-sm font-semibold" id="selectPercent">--%</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span class="text-sm">INSERT</span>
                    </div>
                    <span class="text-sm font-semibold" id="insertPercent">--%</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded"></div>
                        <span class="text-sm">UPDATE</span>
                    </div>
                    <span class="text-sm font-semibold" id="updatePercent">--%</span>
                </div>
            </div>
        </div>

        <!-- Network Traffic -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Network Traffic</h3>
                <div class="text-sm text-gray-600">Bytes per second</div>
            </div>
            <div class="h-48 mb-4">
                <canvas id="networkTrafficChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <div class="text-sm text-gray-600">Received</div>
                    <div class="text-lg font-semibold" id="bytesReceived">-- MB</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Sent</div>
                    <div class="text-lg font-semibold" id="bytesSent">-- MB</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Concerns & Slow Queries -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Performance Concerns</h3>
            <div class="flex space-x-2">
                <button onclick="showTab('slow')" id="tabSlow" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Slow Queries</button>
                <button onclick="showTab('warnings')" id="tabWarnings" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Warnings</button>
                <button onclick="showTab('errors')" id="tabErrors" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Errors</button>
            </div>
        </div>
        
        <div id="slowQueriesContent" class="space-y-4">
            <!-- Slow queries will be dynamically loaded here -->
            <div class="text-center py-8 text-gray-500">
                Loading slow queries...
            </div>
        </div>
        
        <div id="warningsContent" class="space-y-4 hidden">
            <!-- Warnings will be dynamically loaded here -->
            <div class="text-center py-8 text-gray-500">
                No warnings detected
            </div>
        </div>
        
        <div id="errorsContent" class="space-y-4 hidden">
            <!-- Errors will be dynamically loaded here -->
            <div class="text-center py-8 text-gray-500">
                No errors detected
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Global variables
let mysqlData = null;
let charts = {};
let activeTab = 'slow';
const serverId = {{ ($activeServerId ?? ($server->id ?? null)) ?? 'null' }};
const apiBaseUrl = '{{ url("/") }}';

// Initialize charts
function initializeCharts() {
    // Queries Per Second Chart
    const queriesCtx = document.getElementById('queriesChart').getContext('2d');
    charts.queries = new Chart(queriesCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Queries/Second',
                data: [],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Queries Per Second' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Queries/Second'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    }
                }
            }
        }
    });

    // Connections Chart
    const connectionsCtx = document.getElementById('connectionsChart').getContext('2d');
    charts.connections = new Chart(connectionsCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Connections',
                data: [],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Connections' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Connections'
                    }
                }
            }
        }
    });

    // Buffer Pool Chart
    const bufferPoolCtx = document.getElementById('bufferPoolChart').getContext('2d');
    charts.bufferPool = new Chart(bufferPoolCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hit Rate', 'Miss Rate'],
            datasets: [{
                data: [0, 100],
                backgroundColor: ['rgb(34, 197, 94)', 'rgb(239, 68, 68)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Buffer Pool' }
            }
        }
    });

    // Query Types Chart
    const queryTypesCtx = document.getElementById('queryTypesChart').getContext('2d');
    charts.queryTypes = new Chart(queryTypesCtx, {
        type: 'pie',
        data: {
            labels: ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'OTHER'],
            datasets: [{
                data: [0, 0, 0, 0, 100],
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(34, 197, 94)',
                    'rgb(234, 179, 8)',
                    'rgb(239, 68, 68)',
                    'rgb(156, 163, 175)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Query Types Distribution' }
            }
        }
    });

    // Network Traffic Chart
    const networkCtx = document.getElementById('networkTrafficChart').getContext('2d');
    charts.network = new Chart(networkCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Bytes Received',
                    data: [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Bytes Sent',
                    data: [],
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Network Traffic' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Bytes/Second'
                    }
                }
            }
        }
    });
}

// Fetch MySQL data from API
async function fetchMySQLData() {
    try {
        const response = await fetch(`${apiBaseUrl}/admin/server/${serverId}/mysql-data`);
        const data = await response.json();
        if (data.ok) {
            mysqlData = data;
            updateDashboard(data);
            updateCharts(data);
            showApiStatus('success', 'API connected successfully');
        } else {
            showApiStatus('error', data.message || 'Failed to fetch data');
        }
    } catch (error) {
        console.error('Error fetching MySQL data:', error);
        showApiStatus('error', 'Network error - cannot connect to API');
    }
}

// Update dashboard metrics
function updateDashboard(data) {
    const summary = data.summary || {};
    const chartData = data.chartData || {};
    
    // Update metric cards - using correct field names from API
    document.getElementById('currentConnections').textContent = summary.current_connections || summary.threads_connected || '--';
    document.getElementById('connectionsUsage').textContent = summary.connections_percent ? 
        `${summary.connections_percent.toFixed(1)}%` : '--%';
    
    document.getElementById('queriesPerSecond').textContent = summary.queries_per_second ? 
        summary.queries_per_second.toFixed(2) : '--';
    
    document.getElementById('queriesPeak').textContent = chartData.queries ? 
        Math.max(...(chartData.queries.data || [])).toFixed(2) : '--';
    
    document.getElementById('slowQueries').textContent = summary.slow_queries || '--';
    document.getElementById('slowQueriesHour').textContent = summary.slow_queries || '--';
    
    document.getElementById('threadsRunning').textContent = summary.threads_running || '--';
    document.getElementById('threadsRunningCount').textContent = summary.threads_running || '--';
    
    // Update latest values
    document.getElementById('qpsLatest').textContent = `Latest: ${summary.queries_per_second?.toFixed(2) || '--'}`;
    document.getElementById('connectionStats').textContent = `Max: ${summary.max_connections || summary.connections || '--'}`;
    
    // Update buffer pool metrics
    const hitRate = summary.innodb_buffer_pool_hit || 0;
    document.getElementById('bufferPoolHitRate').textContent = `${hitRate.toFixed(1)}%`;
    document.getElementById('bufferPoolReads').textContent = summary.innodb_buffer_pool_reads || '--';
    document.getElementById('bufferPoolRequests').textContent = summary.innodb_buffer_pool_read_requests || '--';
    
    // Update network metrics - already in MB from the summary
    document.getElementById('bytesReceived').textContent = summary.bytes_received ? 
        `${summary.bytes_received.toFixed(2)} MB` : '-- MB';
    document.getElementById('bytesSent').textContent = summary.bytes_sent ? 
        `${summary.bytes_sent.toFixed(2)} MB` : '-- MB';
    
    // Update last updated time
    document.getElementById('lastUpdated').textContent = 
        `Last updated: ${new Date().toLocaleString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        })}`;
}

// Update charts with new data
function updateCharts(data) {
    const chartData = data.chartData || {};
    
    // Update queries chart
    if (chartData.queries) {
        charts.queries.data.labels = chartData.queries.labels || [];
        charts.queries.data.datasets[0].data = chartData.queries.data || [];
        charts.queries.update();
    }
    
    // Update connections chart
    if (chartData.connections) {
        charts.connections.data.labels = chartData.connections.labels || [];
        charts.connections.data.datasets[0].data = chartData.connections.data || [];
        charts.connections.update();
    }
    
    // Update buffer pool chart
    const hitRate = data.summary?.innodb_buffer_pool_hit || 0;
    charts.bufferPool.data.datasets[0].data = [hitRate, 100 - hitRate];
    charts.bufferPool.update();
    
    // Update query types distribution (example data)
    const queryDistribution = [70, 15, 10, 3, 2]; // Example percentages
    charts.queryTypes.data.datasets[0].data = queryDistribution;
    
    // Update percentages in UI
    document.getElementById('selectPercent').textContent = `${queryDistribution[0]}%`;
    document.getElementById('insertPercent').textContent = `${queryDistribution[1]}%`;
    document.getElementById('updatePercent').textContent = `${queryDistribution[2]}%`;
    
    charts.queryTypes.update();
    
    // Update network traffic chart
    if (chartData.buffer_hit) { // Using buffer_hit labels for time
        charts.network.data.labels = chartData.buffer_hit.labels || [];
        // Generate sample network data based on queries data
        const queriesData = chartData.queries?.data || [];
        charts.network.data.datasets[0].data = queriesData.map(val => val * 1000);
        charts.network.data.datasets[1].data = queriesData.map(val => val * 500);
        charts.network.update();
    }
}

// Show API status
function showApiStatus(type, message) {
    const dotWrap = document.getElementById('apiStatusDot');
    if (dotWrap) {
        const dot = dotWrap.querySelector('span') || dotWrap;
        dotWrap.classList.remove('hidden');
        dot.className = `inline-block w-2 h-2 rounded-full ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    }
    showToast(type, type === 'success' ? 'Connected' : (message || 'Connection failed'));
}

function showToast(type, message) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.position = 'fixed';
        container.style.top = '1rem';
        container.style.right = '1rem';
        container.style.zIndex = '9999';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '0.5rem';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `px-3 py-2 rounded-lg shadow-sm border text-sm ${type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => {
        toast.remove();
        if (!container.childElementCount) container.remove();
    }, 3500);
}

// Tab switching
function showTab(tabName) {
    activeTab = tabName;
    
    // Update tab buttons
    document.getElementById('tabSlow').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('tabWarnings').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('tabErrors').classList.remove('bg-blue-600', 'text-white');
    
    document.getElementById('tabSlow').classList.add('bg-gray-200', 'text-gray-700');
    document.getElementById('tabWarnings').classList.add('bg-gray-200', 'text-gray-700');
    document.getElementById('tabErrors').classList.add('bg-gray-200', 'text-gray-700');
    
    document.getElementById(`tab${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`)
        .classList.remove('bg-gray-200', 'text-gray-700');
    document.getElementById(`tab${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`)
        .classList.add('bg-blue-600', 'text-white');
    
    // Show/hide content
    document.getElementById('slowQueriesContent').classList.add('hidden');
    document.getElementById('warningsContent').classList.add('hidden');
    document.getElementById('errorsContent').classList.add('hidden');
    
    document.getElementById(`${tabName}QueriesContent`).classList.remove('hidden');
    
    // Load content for active tab
    loadTabContent(tabName);
}

// Load content for active tab
async function loadTabContent(tabName) {
    const contentEl = document.getElementById(`${tabName}QueriesContent`);
    
    if (tabName === 'slow') {
        // Load slow queries
        contentEl.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Loading slow queries...</p>
            </div>
        `;
        
        const slow = (mysqlData && mysqlData.slowQueries) ? mysqlData.slowQueries : [];
        if (!slow.length) {
            contentEl.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    No slow queries in selected window
                </div>
            `;
            return;
        }
        let html = '<div class="space-y-4">';
        slow.slice(0, 20).forEach(item => {
            const start = item.start_time || '';
            const user = item.user_host || '';
            const duration = item.query_time || '';
            const sql = item.sql_text || '';
            html += `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-yellow-800">${user}</div>
                            <div class="text-sm text-yellow-600 mt-1">Duration: ${duration}</div>
                        </div>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">${start}</span>
                    </div>
                    <code class="block mt-2 text-sm bg-white p-2 rounded border overflow-x-auto">${sql}</code>
                </div>
            `;
        });
        html += '</div>';
        contentEl.innerHTML = html;
    }
}

// Auto-refresh every 30 seconds
setInterval(fetchMySQLData, 30000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    fetchMySQLData();
    showTab('slow');
});
</script>
@endpush
