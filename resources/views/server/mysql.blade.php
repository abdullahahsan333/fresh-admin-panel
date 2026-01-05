@extends($layout ?? 'layouts.admin')

@section('content')
<div class="p-6">
    {{-- 01989225119 ANKON Bai --}}
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
            <button onclick="fetchMySQLData()" class="px-4 py-2 text-blue-600 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
            <div class="text-sm text-gray-500" id="lastUpdated">
                Last updated: {{ now()->format('M d, H:i') }}
            </div>
        </div>
    </div>

    <!-- Top Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Current Connections -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg stroke="currentColor" class="h-6 w-6 text-blue-600" fill="none" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
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
                    <svg stroke="currentColor" class="h-6 w-6 text-purple-600" fill="currentColor" stroke-width="0" viewBox="0 0 32 32">
                        <path d="M 20.21875 5 C 18.539063 5 17.15625 6.382813 17.15625 8.0625 C 17.15625 9.742188 18.539063 11.125 20.21875 11.125 C 21.902344 11.125 23.3125 9.742188 23.3125 8.0625 C 23.3125 6.382813 21.902344 5 20.21875 5 Z M 20.21875 7 C 20.820313 7 21.3125 7.464844 21.3125 8.0625 C 21.3125 8.660156 20.820313 9.125 20.21875 9.125 C 19.621094 9.125 19.15625 8.664063 19.15625 8.0625 C 19.15625 7.464844 19.621094 7 20.21875 7 Z M 12.9375 9 C 12.457031 9.058594 11.972656 9.28125 11.625 9.65625 L 8.25 13.3125 L 9.75 14.6875 L 13.09375 11.03125 C 13.128906 10.996094 13.175781 10.972656 13.21875 11 L 14.8125 12.0625 L 12.46875 15.3125 C 11.734375 16.34375 11.855469 17.761719 12.75 18.65625 L 16.28125 22.1875 L 13.375 28 L 15.625 28 L 18.09375 23.09375 C 18.480469 22.324219 18.328125 21.390625 17.71875 20.78125 L 14.1875 17.25 C 13.984375 17.046875 13.957031 16.703125 14.125 16.46875 L 16.46875 13.1875 L 17.28125 13.71875 L 18.875 16.125 C 19.246094 16.679688 19.863281 17 20.53125 17 L 25 17 L 25 15 L 20.53125 15 L 18.84375 12.4375 L 18.71875 12.28125 L 18.5625 12.15625 L 14.34375 9.34375 C 13.917969 9.058594 13.417969 8.941406 12.9375 9 Z M 12.0625 19.53125 L 10.59375 21 L 6 21 L 6 23 L 10.59375 23 C 11.121094 23 11.625 22.785156 12 22.40625 L 13.46875 20.9375 Z"></path>
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
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Queries Per Second</h3>
            </div>
            <div class="h-64 mb-4">
                <canvas id="queriesChart"></canvas>
            </div>
            <div class="mt-4 flex items-center">
                <span class="text-3xl font-bold text-gray-800 mr-2" id="qpsLatestBig">--</span>
                <span class="text-sm text-gray-500">Queries / Second</span>
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
                    <div class="text-sm text-gray-600">Network Input (MB)</div>
                    <div class="text-lg font-semibold" id="bytesReceived">-- MB</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Network Output (MB)</div>
                    <div class="text-lg font-semibold" id="bytesSent">-- MB</div>
                </div>
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

        <!-- Connection Usage -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-2">
                <h3 class="text-lg font-semibold text-gray-800">MySQL Connection Usage</h3>
            </div>
            <div class="flex flex-col items-center">
                <div class="relative h-28 w-28 my-4">
                    <canvas id="connectionsUsageChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xl font-bold text-gray-800" id="connectionsUsageCenter">--% Usage</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6 text-center">
                <div>
                    <div class="flex items-center justify-center space-x-2">
                        <span class="inline-block w-5 h-5 rounded bg-green-100"></span>
                        <div class="text-sm text-gray-600">Current Connections</div>
                    </div>
                    <div class="text-lg font-semibold" id="connCurrent">--</div>
                </div>
                <div>
                    <div class="flex items-center justify-center space-x-2">
                        <span class="inline-block w-5 h-5 rounded bg-blue-100"></span>
                        <div class="text-sm text-gray-600">Max Connections</div>
                    </div>
                    <div class="text-lg font-semibold" id="connMax">--</div>
                </div>
            </div>
        </div>

        <!-- Load vs Query Time -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="mb-2">
                    <h3 class="text-lg font-semibold text-gray-800">Load vs Query Time</h3>
                </div>
                <div class="h-48">
                    <canvas id="loadVsQueryChart"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4 text-center">
                    <div>
                        <div class="text-sm text-gray-600">Queries / Second</div>
                        <div class="text-lg font-semibold" id="loadQpsLatest">--</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Avg Query Time (ms)</div>
                        <div class="text-lg font-semibold" id="loadAvgLatest">--</div>
                    </div>
                </div>
            </div>

        <!-- Table Open Cache -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-2">
                <h3 class="text-lg font-semibold text-gray-800">Table Open Cache</h3>
            </div>
            <div class="h-48 mb-4">
                <canvas id="tableOpenCacheChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6 text-center">
                <div>
                    <div class="flex items-center justify-center space-x-2">
                        <span class="inline-block w-5 h-5 rounded bg-purple-100"></span>
                        <div class="text-sm text-gray-600">Open Tables</div>
                    </div>
                    <div class="text-lg font-semibold" id="openTables">--</div>
                </div>
                <div>
                    <div class="flex items-center justify-center space-x-2">
                        <span class="inline-block w-5 h-5 rounded bg-blue-100"></span>
                        <div class="text-sm text-gray-600">Table Open Cache</div>
                    </div>
                    <div class="text-lg font-semibold" id="tableOpenCache">--</div>
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
    </div>

    <!-- Performance Concerns - UPDATED SECTION -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Performance Concerns</h3>
            <div class="flex space-x-2">
                <button onclick="showConcernTab('slow')" id="tabConcernSlow" class="px-4 py-2 btn-primary rounded-lg text-sm">Slow Queries</button>
                <button onclick="showConcernTab('warnings')" id="tabConcernWarnings" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Warnings</button>
                <button onclick="showConcernTab('errors')" id="tabConcernErrors" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Errors</button>
            </div>
        </div>
        
        <!-- Slow Queries Tab Content -->
        <div id="concernSlowContent" class="space-y-4">
            <div class="text-center py-8 text-gray-500">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Loading slow queries...</p>
            </div>
        </div>

        <!-- Warnings Tab Content -->
        <div id="concernWarningsContent" class="space-y-4 hidden">
            <div class="text-center py-8 text-gray-500">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Loading warnings...</p>
            </div>
        </div>

        <!-- Errors Tab Content -->
        <div id="concernErrorsContent" class="space-y-4 hidden">
            <div class="text-center py-8 text-gray-500">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Loading errors...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global variables
let mysqlData = null;
let charts = {};
let activeConcernTab = 'slow';
const serverId = {{ ($activeServerId ?? ($server->id ?? null)) ?? 'null' }};
const panel = "{{ $panel ?? 'admin' }}";
const apiBaseUrl = '';

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
                legend: { display: false }
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

    // Connection Usage Gauge
    (function(){
        const el = document.getElementById('connectionsUsageChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        let r = 59, g = 130, b = 246;
        try {
            const v = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim();
            const parts = v.split(' ').filter(Boolean);
            if (parts.length === 3) {
                r = parseInt(parts[0], 10);
                g = parseInt(parts[1], 10);
                b = parseInt(parts[2], 10);
            }
        } catch(e) {}
        const usedColor = `rgb(${r}, ${g}, ${b})`;
        const usedBg = `rgba(${r}, ${g}, ${b}, 0.15)`;
        charts.connUsage = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Used', 'Free'],
                datasets: [{
                    data: [0, 100],
                    backgroundColor: [usedColor, 'rgba(148, 163, 184, 0.25)'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    })();

    // Buffer Pool Chart
    const bufferPoolCtx = document.getElementById('bufferPoolChart').getContext('2d');
    charts.bufferPool = new Chart(bufferPoolCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Hit %',
                    data: [],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.15)',
                    borderWidth: 2,
                    tension: 0.25,
                    fill: false,
                    pointRadius: 0
                },
                {
                    label: 'Miss %',
                    data: [],
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.15)',
                    borderWidth: 2,
                    tension: 0.25,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: function(v){ return v + '%'; } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });

    // Load vs Query Time (Pie)
    (function(){
        const el = document.getElementById('loadVsQueryChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        charts.loadVsQuery = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Queries / Second', 'Avg Query Time (ms)'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: ['rgba(245, 158, 11, 0.8)', 'rgba(59, 130, 246, 0.8)'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    })();

    // Table Open Cache Line Chart
    (function(){
        const el = document.getElementById('tableOpenCacheChart');
        if (!el) return;
        const ctx = el.getContext('2d');
    charts.toc = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Usage %',
                data: [],
                borderColor: 'rgb(124, 58, 237)',
                backgroundColor: 'rgba(124, 58, 237, 0.15)',
                borderWidth: 2,
                tension: 0.25,
                fill: false,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: function(v){ return v + '%'; } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
    })();
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
                legend: { position: 'bottom' }
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
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Speed'
                    },
                    ticks: {
                        callback: function(value) {
                            if (value === 0) return '0 B/s';
                            const units = ['B/s', 'KB/s', 'MB/s', 'GB/s'];
                            let i = 0;
                            while (value >= 1024 && i < units.length - 1) {
                                value /= 1024;
                                i++;
                            }
                            return value.toFixed(1) + ' ' + units[i];
                        }
                    }
                }
            }
        }
    });
}

// Concern Tab Management
function showConcernTab(tabName) {
    activeConcernTab = tabName;
    
    // Update tab buttons
    const btnSlow = document.getElementById('tabConcernSlow');
    const btnWarn = document.getElementById('tabConcernWarnings');
    const btnErr = document.getElementById('tabConcernErrors');
    
    // Reset all buttons
    [btnSlow, btnWarn, btnErr].forEach(btn => {
        if (btn) {
            btn.classList.remove('btn-primary');
            btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
        }
    });
    
    // Activate current tab button
    const activeTabId = 'tabConcern' + tabName.charAt(0).toUpperCase() + tabName.slice(1);
    const activeBtn = document.getElementById(activeTabId);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
        activeBtn.classList.add('btn-primary');
    }
    
    // Show/hide content
    const slowEl = document.getElementById('concernSlowContent');
    const warnEl = document.getElementById('concernWarningsContent');
    const errEl = document.getElementById('concernErrorsContent');
    
    [slowEl, warnEl, errEl].forEach(el => {
        if (el) el.classList.add('hidden');
    });
    
    const targetEl = document.getElementById(`concern${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Content`);
    if (targetEl) {
        targetEl.classList.remove('hidden');
    }
    
    // Load content for active tab
    loadConcernTabContent(tabName);
}

// Load content for active concern tab
async function loadConcernTabContent(tabName) {
    const contentEl = document.getElementById(`concern${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Content`);
    
    if (tabName === 'slow') {
        // Show loading state
        contentEl.innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
                <p class="text-gray-600 font-medium">Loading slow queries...</p>
                <p class="text-sm text-gray-500 mt-2">Fetching data from the database</p>
            </div>
        `;
        
        try {
            const response = await fetch(`${apiBaseUrl}/${panel}/server/${serverId}/mysql-slow-queries?minutes=15`);
            const data = await response.json();
            
            if (data.ok && data.slowQueries && Array.isArray(data.slowQueries) && data.slowQueries.length > 0) {
                const slowQueries = data.slowQueries;
                renderSlowQueries(contentEl, slowQueries);
            } else {
                let message = 'No slow queries detected in the selected time window';
                if (data.message) {
                    message = data.message;
                }
                contentEl.innerHTML = `
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-600 font-medium">No Slow Queries</p>
                        <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">${message}</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading slow queries:', error);
            contentEl.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-red-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-600 font-medium">Unable to Load Slow Queries</p>
                    <p class="text-sm text-gray-500 mt-2">Network or server error occurred</p>
                    <button onclick="loadConcernTabContent('slow')" class="mt-4 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 text-sm font-medium">
                        Retry
                    </button>
                </div>
            `;
        }
    } else if (tabName === 'warnings') {
        contentEl.innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-yellow-200 border-t-yellow-600 mb-4"></div>
                <p class="text-gray-600 font-medium">Loading MySQL warnings...</p>
                <p class="text-sm text-gray-500 mt-2">Checking for database warnings</p>
            </div>
        `;
        
        try {
            // Fetch MySQL warnings from dedicated endpoint
            const response = await fetch(`/${panel}/server/${serverId}/mysql-warnings`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            
            if (data.ok && Array.isArray(data.warnings)) {
                renderWarningsTab(contentEl, data.warnings);
            } else {
                renderDefaultWarnings(contentEl, data.message || 'Unable to load warnings');
            }
        } catch (error) {
            console.error('Error loading warnings:', error);
            renderDefaultWarnings(contentEl, 'Network error occurred');
        }
    } else if (tabName === 'errors') {
        contentEl.innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-red-200 border-t-red-600 mb-4"></div>
                <p class="text-gray-600 font-medium">Loading MySQL errors...</p>
                <p class="text-sm text-gray-500 mt-2">Checking for database errors</p>
            </div>
        `;
        
        try {
            // Fetch MySQL errors from dedicated endpoint
            const response = await fetch(`/${panel}/server/${serverId}/mysql-errors`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            
            if (data.ok && Array.isArray(data.errors)) {
                renderErrorsTab(contentEl, data.errors);
            } else {
                renderDefaultErrors(contentEl, data.message || 'Unable to load errors');
            }
        } catch (error) {
            console.error('Error loading errors:', error);
            renderDefaultErrors(contentEl, 'Network error occurred');
        }
    }
}

function renderWarningsTab(containerEl, warnings) {
    if (!warnings || warnings.length === 0) {
        containerEl.innerHTML = `
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-100 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-lg font-semibold text-green-800 mb-2">No Active Warnings</h4>
                            <p class="text-green-700">All MySQL metrics are within normal operating ranges.</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="space-y-4">
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    ${warnings.length} warning${warnings.length !== 1 ? 's' : ''} detected
                </span>
            </div>
    `;
    
    warnings.forEach(warning => {
        const severityClass = warning.severity === 'high' ? 'border-red-200 bg-red-50' :
                            warning.severity === 'medium' ? 'border-yellow-200 bg-yellow-50' :
                            'border-blue-200 bg-blue-50';
        const severityText = warning.severity === 'high' ? 'High Priority' :
                            warning.severity === 'medium' ? 'Medium Priority' :
                            'Low Priority';
        
        html += `
            <div class="border rounded-lg p-4 ${severityClass}">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 ${warning.severity === 'high' ? 'text-red-500' : warning.severity === 'medium' ? 'text-yellow-500' : 'text-blue-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">${warning.type}</h4>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${warning.severity === 'high' ? 'bg-red-100 text-red-800' : warning.severity === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'}">
                                    ${severityText}
                                </span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-700">${warning.message}</p>
                        <div class="mt-2 p-2 bg-white rounded border text-sm">
                            <span class="font-medium text-gray-700">Suggestion:</span> ${warning.suggestion}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `</div>`;
    containerEl.innerHTML = html;
}

function renderDefaultWarnings(containerEl, message) {
    containerEl.innerHTML = `
        <div class="space-y-4">
            <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-6">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <h4 class="text-lg font-semibold text-yellow-800 mb-2">Unable to Load Warnings</h4>
                        <p class="text-yellow-700">${message}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderErrorsTab(containerEl, errors) {
    if (!errors || errors.length === 0) {
        containerEl.innerHTML = `
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-100 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        <div>
                            <h4 class="text-lg font-semibold text-green-800 mb-2">No Critical Errors</h4>
                            <p class="text-green-700">MySQL database is operating without critical errors.</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="space-y-4">
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    ${errors.length} error${errors.length !== 1 ? 's' : ''} detected
                </span>
            </div>
    `;
    
    errors.forEach(error => {
        const severityClass = error.severity === 'critical' ? 'border-red-300 bg-red-50' :
                            error.severity === 'high' ? 'border-red-200 bg-red-50' :
                            'border-orange-200 bg-orange-50';
        const severityText = error.severity === 'critical' ? 'Critical' :
                            error.severity === 'high' ? 'High Priority' :
                            'Medium Priority';
        
        html += `
            <div class="border rounded-lg p-4 ${severityClass}">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 ${error.severity === 'critical' ? 'text-red-600' : error.severity === 'high' ? 'text-red-500' : 'text-orange-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">${error.type}</h4>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${error.severity === 'critical' ? 'bg-red-200 text-red-900' : error.severity === 'high' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800'}">
                                    ${severityText}
                                </span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-700">${error.message}</p>
                        <div class="mt-2 p-2 bg-white rounded border border-red-100 text-sm">
                            <span class="font-medium text-gray-700">Recommended Action:</span> ${error.action || error.suggestion}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `</div>`;
    containerEl.innerHTML = html;
}

function renderDefaultErrors(containerEl, message) {
    containerEl.innerHTML = `
        <div class="space-y-4">
            <div class="bg-red-50 border border-red-100 rounded-lg p-6">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-lg font-semibold text-red-800 mb-2">Unable to Load Error Information</h4>
                        <p class="text-red-700">${message}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderSlowQueries(containerEl, slowQueries) {
    if (!slowQueries || slowQueries.length === 0) {
        containerEl.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-600 font-medium">No Slow Queries Found</p>
                <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">No queries exceeded the slow query threshold in the selected time window.</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="timeline-container">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 01118 0z" />
                        </svg>
                        ${slowQueries.length} slow queries detected
                    </span>
                </div>
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Time Window:</span> Last 15 minutes
                </div>
            </div>
            
            <div class="mb-4">
                <div class="flex items-center space-x-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                        <span>Critical (>10s)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-orange-500 mr-2"></div>
                        <span>High (5-10s)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                        <span>Medium (2-5s)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                        <span>Low (<2s)</span>
                    </div>
                </div>
            </div>
            
            <ul class="space-y-6">
    `;
    
    // Sort by start_time descending (newest first)
    slowQueries.sort((a, b) => {
        const timeA = new Date(a.start_time || '');
        const timeB = new Date(b.start_time || '');
        return timeB - timeA;
    });
    
    // Limit to 20 queries for performance
    const limitedQueries = slowQueries.slice(0, 20);
    
    limitedQueries.forEach((query, index) => {
        const startTime = query.start_time || '';
        const userHost = query.user_host || '';
        const queryTime = query.query_time || '';
        const sqlText = query.sql_text || '';
        
        // Extract execution time in seconds
        let execTimeSeconds = 0;
        let execTimeFormatted = queryTime;
        if (queryTime) {
            // Parse HH:MM:SS.ssssss format
            const timeParts = queryTime.split(':');
            if (timeParts.length === 3) {
                const hours = parseFloat(timeParts[0]) || 0;
                const minutes = parseFloat(timeParts[1]) || 0;
                const seconds = parseFloat(timeParts[2]) || 0;
                execTimeSeconds = hours * 3600 + minutes * 60 + seconds;
                execTimeFormatted = `${execTimeSeconds.toFixed(3)}s`;
            }
        }
        
        // Determine severity based on execution time
        let severityColor = '';
        let severityBg = '';
        let severityText = '';
        if (execTimeSeconds > 10) {
            severityColor = 'text-red-600';
            severityBg = 'bg-red-100';
            severityText = 'Critical';
        } else if (execTimeSeconds > 5) {
            severityColor = 'text-orange-600';
            severityBg = 'bg-orange-100';
            severityText = 'High';
        } else if (execTimeSeconds > 2) {
            severityColor = 'text-yellow-600';
            severityBg = 'bg-yellow-100';
            severityText = 'Medium';
        } else {
            severityColor = 'text-blue-600';
            severityBg = 'bg-blue-100';
            severityText = 'Low';
        }
        
        // Format date if available
        let formattedDate = '';
        let formattedTime = '';
        if (startTime) {
            try {
                const date = new Date(startTime);
                formattedDate = date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
                formattedTime = date.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });
            } catch (e) {
                formattedDate = startTime.split('T')[0] || startTime;
                formattedTime = startTime.split('T')[1]?.split('.')[0] || '';
            }
        }
        
        // Extract username from user_host format: username[username] @ [IP]
        let username = 'Unknown';
        let ipAddress = '';
        if (userHost) {
            const atIndex = userHost.indexOf('@');
            if (atIndex > 0) {
                // Get the part before @ (e.g., "livoadmin[livoadmin]" or "root")
                let userPart = userHost.substring(0, atIndex).trim();
                
                // If it contains brackets like "user[user]", just take the first part
                const bracketIndex = userPart.indexOf('[');
                if (bracketIndex > 0) {
                    username = userPart.substring(0, bracketIndex).trim();
                } else {
                    username = userPart;
                }
                
                // Get the part after @ (IP address)
                ipAddress = userHost.substring(atIndex + 1).replace(/[\[\]]/g, '').trim();
            } else {
                username = userHost;
            }
        }
        
        // Truncate SQL for preview
        const sqlPreview = sqlText.length > 200 ? sqlText.substring(0, 200) + '...' : sqlText;
        
        html += `
            <li class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="p-5">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 mb-4">
                        <div class="flex-1">
                            <div class="flex items-center flex-wrap gap-2 mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${severityBg} ${severityColor}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    ${severityText} - ${execTimeFormatted}
                                </span>
                                
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    ${username}
                                </span>
                                
                                ${ipAddress ? `
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3" />
                                    </svg>
                                    ${ipAddress}
                                </span>
                                ` : ''}
                            </div>
                            
                            <div class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Execution Time:</span> ${queryTime}
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 mb-1">${formattedDate}</div>
                            <div class="text-sm text-gray-500">${formattedTime}</div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">SQL Query</h4>
                        <div class="relative overflow-hidden border border-gray-200 rounded-lg">
                            <div class="bg-gray-900 p-4">
                                <pre class="text-sm text-white font-mono whitespace-pre-wrap break-all leading-relaxed max-h-60 overflow-y-auto" 
                                     id="sqlText-${index}" 
                                     data-full-text="${escapeHtml(sqlText)}" 
                                     data-is-full="false">${escapeHtml(sqlPreview)}</pre>
                                
                                ${sqlText.length > 200 ? `
                                <div class="mt-3">
                                    <button onclick="toggleFullSql(${index})" class="text-sm text-yellow-400 hover:text-yellow-300 font-medium flex items-center" id="toggleBtn-${index}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                        Show Full Query
                                    </button>
                                </div>
                                ` : ''}
                            </div>
                            
                            <div class="p-2 bg-gray-900 flex items-center justify-between text-xs text-white">
                                <span>Length: ${sqlText.length} characters</span>
                                <button onclick="copyToClipboard(${index}, \`${escapeHtml(sqlText)}\`)" class="text-primary-400 hover:text-primary-800 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        `;
    });
    
    html += `
            </ul>
        </div>
    `;
    
    containerEl.innerHTML = html;
}

// Helper functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function toggleFullSql(index) {
    const sqlTextEl = document.getElementById('sqlText-' + index);
    const toggleBtn = document.getElementById('toggleBtn-' + index);
    const fullText = sqlTextEl.dataset.fullText;
    
    if (!fullText) return;
    
    if (sqlTextEl.dataset.isFull === 'true') {
        // Show preview
        const preview = fullText.length > 200 ? fullText.substring(0, 200) + '...' : fullText;
        sqlTextEl.innerHTML = escapeHtml(preview);
        sqlTextEl.dataset.isFull = 'false';
        toggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            Show Full Query
        `;
    } else {
        // Show full text
        sqlTextEl.innerHTML = escapeHtml(fullText);
        sqlTextEl.dataset.isFull = 'true';
        toggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
            Show Less
        `;
    }
}

function copyToClipboard(index, sqlText) {
    // Create a temporary textarea
    const textarea = document.createElement('textarea');
    textarea.value = sqlText;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            // Show success message
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Copied!
            `;
            btn.classList.remove('text-blue-600', 'hover:text-blue-800');
            btn.classList.add('text-green-600');
            
            // Reset after 2 seconds
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('text-green-600');
                btn.classList.add('text-blue-600', 'hover:text-blue-800');
            }, 2000);
        }
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
    
    document.body.removeChild(textarea);
}

// Fetch MySQL data from API
async function fetchMySQLData() {
    try {
        const response = await fetch(`/${panel}/server/${serverId}/mysql-data`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const data = await response.json();
        if (data.ok) {
            mysqlData = data;
            updateDashboard(data);
            updateCharts(data);
            showApiStatus('success', 'API connected successfully');
            
            // Update concern tabs if they're visible
            if (activeConcernTab === 'slow' && data.slowQueries) {
                const contentEl = document.getElementById('concernSlowContent');
                if (contentEl && !contentEl.classList.contains('hidden')) {
                    renderSlowQueries(contentEl, data.slowQueries);
                }
            }
        } else {
            showApiStatus('error', data.message || 'Failed to fetch data');
        }
    } catch (error) {
        console.error('Error fetching MySQL data:', error);
        showApiStatus('error', 'Network error - cannot connect to API');
    }
}

// Format network speed
function formatNetworkSpeed(bytesPerSec) {
    if (bytesPerSec === undefined || bytesPerSec === null) return '--';
    if (bytesPerSec === 0) return '0 B/s';
    
    const units = ['B/s', 'KB/s', 'MB/s', 'GB/s'];
    let i = 0;
    while (bytesPerSec >= 1024 && i < units.length - 1) {
        bytesPerSec /= 1024;
        i++;
    }
    return `${bytesPerSec.toFixed(2)} ${units[i]}`;
}

// Update dashboard metrics
function updateDashboard(data) {
    const summary = data.summary || {};
    const chartData = data.chartData || {};
    
    // Update metric cards - using correct field names from API
    const topCurrentEl = document.getElementById('currentConnections');
    if (topCurrentEl) topCurrentEl.textContent = summary.current_connections || summary.threads_connected || '--';
    const bottomCurrentEl = document.getElementById('connCurrent');
    if (bottomCurrentEl) bottomCurrentEl.textContent = summary.current_connections || summary.threads_connected || '--';
    const bottomMaxEl = document.getElementById('connMax');
    if (bottomMaxEl) bottomMaxEl.textContent = summary.max_connections || '--';
    
    const usageEl = document.getElementById('connectionsUsage');
    if (usageEl) {
        const p = summary.connections_percent;
        usageEl.textContent = (typeof p === 'number' && !isNaN(p)) ? `${p.toFixed(1)}%` : '--%';
    }
    
    document.getElementById('queriesPerSecond').textContent = summary.queries_per_second ? 
        summary.queries_per_second.toFixed(2) : '--';
    
    document.getElementById('queriesPeak').textContent = chartData.queries ? 
        Math.max(...(chartData.queries.data || [])).toFixed(2) : '--';
    
    document.getElementById('slowQueries').textContent = summary.slow_queries || '--';
    document.getElementById('slowQueriesHour').textContent = summary.slow_queries || '--';
    
    document.getElementById('threadsRunning').textContent = summary.threads_running || '--';
    document.getElementById('threadsRunningCount').textContent = summary.threads_running || '--';
    
    // Update latest values (Big numbers below charts)
    document.getElementById('qpsLatestBig').textContent = summary.queries_per_second ? 
        summary.queries_per_second.toFixed(2) : '--';
        
    const connCenterEl = document.getElementById('connectionsUsageCenter');
    if (connCenterEl) {
        connCenterEl.innerHTML = summary.connections_percent ? 
            `${summary.connections_percent.toFixed(1)}%` : '--% Usage';
    }
    if (charts.connUsage) {
        const p = summary.connections_percent || 0;
        charts.connUsage.data.datasets[0].data = [p, Math.max(0, 100 - p)];
        charts.connUsage.update();
    }
    
    // Update buffer pool metrics
    const hitRate = summary.innodb_buffer_pool_hit || 0;
    document.getElementById('bufferPoolHitRate').textContent = `${hitRate.toFixed(1)}%`;
    const bufferPoolCenterEl = document.getElementById('bufferPoolCenter');
    if (bufferPoolCenterEl) bufferPoolCenterEl.textContent = `${hitRate.toFixed(1)}%`;
    document.getElementById('bufferPoolReads').textContent = summary.innodb_buffer_pool_reads || '--';
    document.getElementById('bufferPoolRequests').textContent = summary.innodb_buffer_pool_read_requests || '--';
    
    // Table Open Cache metrics
    const openTables = summary.open_tables ?? 0;
    const tableOpenCache = summary.table_open_cache ?? 0;
    const tocPercent = tableOpenCache > 0 ? (openTables / tableOpenCache * 100) : 0;
    document.getElementById('openTables').textContent = openTables || '--';
    document.getElementById('tableOpenCache').textContent = tableOpenCache || '--';
    const tocCenterEl = document.getElementById('tableOpenCacheCenter');
    if (tocCenterEl) {
        tocCenterEl.innerHTML = `${tocPercent.toFixed(1)}% Usage`;
    }
    // Chart update for Table Open Cache handled in updateCharts (line chart)
    
    // Update network metrics - show MB totals
    const recvMb = (typeof summary.bytes_received === 'number') ? summary.bytes_received.toFixed(2) : '--';
    const sentMb = (typeof summary.bytes_sent === 'number') ? summary.bytes_sent.toFixed(2) : '--';
    document.getElementById('bytesReceived').textContent = `${recvMb} MB`;
    document.getElementById('bytesSent').textContent = `${sentMb} MB`;
    
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
    
    // Connection usage gauge is updated in updateDashboard via summary
    
    // Update buffer pool chart (line)
    const hitRate = data.summary?.innodb_buffer_pool_hit || 0;
    if (charts.bufferPool) {
        const label = new Date().toLocaleTimeString();
        charts.bufferPool.data.labels.push(label);
        charts.bufferPool.data.datasets[0].data.push(hitRate);
        charts.bufferPool.data.datasets[1].data.push(Math.max(0, 100 - hitRate));
        if (charts.bufferPool.data.labels.length > 30) {
            charts.bufferPool.data.labels.shift();
            charts.bufferPool.data.datasets[0].data.shift();
            charts.bufferPool.data.datasets[1].data.shift();
        }
        charts.bufferPool.update();
    }
    
    // Update Table Open Cache chart (line)
    if (charts.toc) {
        const openTables = data.summary?.open_tables ?? 0;
        const tableOpenCache = data.summary?.table_open_cache ?? 0;
        const tocPercent = tableOpenCache > 0 ? (openTables / tableOpenCache * 100) : 0;
        const label = new Date().toLocaleTimeString();
        charts.toc.data.labels.push(label);
        charts.toc.data.datasets[0].data.push(tocPercent);
        if (charts.toc.data.labels.length > 30) {
            charts.toc.data.labels.shift();
            charts.toc.data.datasets[0].data.shift();
        }
        charts.toc.update();
    }
    
    // Update query types distribution (example data)
    const queryDistribution = [70, 15, 10, 3, 2]; // Example percentages
    charts.queryTypes.data.datasets[0].data = queryDistribution;
    
    // Update percentages in UI
    document.getElementById('selectPercent').textContent = `${queryDistribution[0]}%`;
    document.getElementById('insertPercent').textContent = `${queryDistribution[1]}%`;
    document.getElementById('updatePercent').textContent = `${queryDistribution[2]}%`;
    
    charts.queryTypes.update();
    
    // Update network traffic chart
    if (chartData.network) {
        charts.network.data.labels = chartData.network.labels || [];
        charts.network.data.datasets[0].data = chartData.network.received || [];
        charts.network.data.datasets[1].data = chartData.network.sent || [];
        charts.network.update();
    }
    
    // Update Load vs Query Time chart (pie uses latest values)
    if (charts.loadVsQuery && chartData.queries) {
        const qpsArr = chartData.queries.data || [];
        const avgArr = (chartData.avg_query_time_ms && chartData.avg_query_time_ms.data) ? chartData.avg_query_time_ms.data : [];
        const latestQps = qpsArr.length ? qpsArr[qpsArr.length - 1] : 0;
        const latestAvg = avgArr.length ? avgArr[avgArr.length - 1] : 0;
        charts.loadVsQuery.data.datasets[0].data = [latestQps, latestAvg];
        charts.loadVsQuery.update();
        const qpsEl = document.getElementById('loadQpsLatest');
        const avgEl = document.getElementById('loadAvgLatest');
        if (qpsEl) qpsEl.textContent = typeof latestQps === 'number' ? latestQps.toFixed(2) : '--';
        if (avgEl) avgEl.textContent = typeof latestAvg === 'number' ? latestAvg.toFixed(2) : '--';
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

// Add CSS for better styling
const style = document.createElement('style');
style.textContent = `
    .timeline-container {
        position: relative;
    }
    
    .sql-container {
        position: relative;
    }
    
    .sql-container pre {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 13px;
        line-height: 1.5;
        tab-size: 2;
    }
    
    .sql-container pre::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .sql-container pre::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .sql-container pre::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .sql-container pre::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);

// Auto-refresh every 30 seconds
setInterval(fetchMySQLData, 30000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    fetchMySQLData();
    showConcernTab('slow'); // Initialize concern tab
});
</script>
@endpush
