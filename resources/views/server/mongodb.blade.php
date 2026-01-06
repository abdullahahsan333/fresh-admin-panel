@extends($layout ?? 'layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">MongoDB</h1>
        <div id="apiStatusDot" class="hidden">
            <span class="inline-block w-2 h-2 rounded-full"></span>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">Project: {{ $server->project->name ?? 'N/A' }}</span>
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded">Server: {{ $server->ip }}</span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: <span id="lastUpdated">{{ now()->format('M d, H:i') }}</span></div>
        <button id="refreshBtn" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </button>
    </div>
</header>

<!-- Top Row: 4 Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Current Connections -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-[rgb(var(--color-primary)/.12)] flex items-center justify-center text-[rgb(var(--color-primary))]">
                    <i class="ri-user-line"></i>
                </div>
                <span class="text-xl font-semibold text-gray-800" id="currentConnections">--</span>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Usage</div>
                <div class="text-sm font-semibold text-gray-800" id="connectionsUsageRight">--%</div>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700">Current Connections</div>
        <div class="text-xs text-gray-500">Active connections</div>
    </div>

    <!-- Queries / Sec -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-green-100 flex items-center justify-center text-green-600">
                    <i class="ri-flashlight-line"></i>
                </div>
                <span class="text-xl font-semibold text-gray-800" id="queriesPerSec">--</span>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Peak</div>
                <div class="text-sm font-semibold text-gray-800" id="queriesPeak">--</div>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700">Queries / Second</div>
        <div class="text-xs text-gray-500">Query operations per second</div>
    </div>

    <!-- Memory Usage -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-red-100 flex items-center justify-center text-red-600">
                    <i class="ri-computer-line"></i>
                </div>
                <span class="text-xl font-semibold text-gray-800" id="memoryUsage">--</span>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Virtual</div>
                <div class="text-sm font-semibold text-gray-800" id="memoryVirtual">--</div>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700">Memory Usage</div>
        <div class="text-xs text-gray-500">Resident memory (MB)</div>
    </div>

    <!-- Available Connections -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-500">
                    <i class="ri-plug-line"></i>
                </div>
                <span class="text-xl font-semibold text-gray-800" id="availableConnections">--</span>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Active</div>
                <div class="text-sm font-semibold text-gray-800" id="activeClientsTop">--</div>
            </div>
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
    
    <!-- Global Lock Status -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-gray-700 font-medium">Global Lock Status</h3>
            <div class="text-xs text-gray-500">Current Queue</div>
        </div>
        <div id="mongoGlobalLockStatus" class="h-48"></div>
        <div class="mt-6">
            <div class="text-sm font-medium text-gray-700 mb-2">Lock Status</div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-100 text-purple-600">R</span>
                    <div>
                        <div class="text-xs text-gray-500">Active Readers</div>
                        <div class="text-base font-semibold text-gray-800" id="globalActiveReaders">0</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600">W</span>
                    <div>
                        <div class="text-xs text-gray-500">Active Writers</div>
                        <div class="text-base font-semibold text-gray-800" id="globalActiveWriters">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Network Traffic -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-6">
            <h3 class="text-gray-700 font-medium">Network Traffic</h3>
            <div class="text-xs text-gray-500 mt-1" id="networkTrafficTime">Last 60 minutes</div>
        </div>
        
        <div class="mt-6">
            <div id="networkTrafficChart" class="h-48"></div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600 mb-1">Bytes In</div>
                <div class="text-lg font-bold text-gray-800" id="bytesIn">0 MB</div>
                <div class="text-xs text-gray-500">Total received</div>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <div class="text-sm text-gray-600 mb-1">Bytes Out</div>
                <div class="text-lg font-bold text-gray-800" id="bytesOut">0 MB</div>
                <div class="text-xs text-gray-500">Total sent</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-6">
        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">Sent <span id="bytesSentUnit" class="text-xs text-gray-500"></span></h3>
                <div class="text-xs text-gray-500 mt-1" id="bytesSentLatest">--</div>
            </div>
            <div class="h-20 mb-3">
                <div id="bytesSentChart" class="h-full"></div>
            </div>
            
        </div>

        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">Received <span id="bytesReceivedUnit" class="text-xs text-gray-500"></span></h3>
                <div class="text-xs text-gray-500 mt-1" id="bytesReceivedLatest">--</div>
            </div>
            <div class="h-20 mb-3">
                <div id="bytesReceivedChart" class="h-full"></div>
            </div>
            
        </div>
    </div>
    
    <!-- MongoDB Operations Overview -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-gray-700 font-medium">Query Types</h3>
            <div class="text-sm text-gray-500">Distribution</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <div class="h-48">
                <div id="mongoOpsPieChart" class="h-full"></div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="ri-search-line text-gray-500"></i>
                        <span>Query Operations</span>
                    </div>
                    <div class="text-gray-800 font-semibold" id="opsQueryCount">--</div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="ri-add-line text-gray-500"></i>
                        <span>Insert Operations</span>
                    </div>
                    <div class="text-gray-800 font-semibold" id="opsInsertCount">--</div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="ri-refresh-line text-gray-500"></i>
                        <span>Update Operations</span>
                    </div>
                    <div class="text-gray-800 font-semibold" id="opsUpdateCount">--</div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="ri-delete-bin-line text-gray-500"></i>
                        <span>Delete Operations</span>
                    </div>
                    <div class="text-gray-800 font-semibold" id="opsDeleteCount">--</div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="ri-terminal-line text-gray-500"></i>
                        <span>Command Operations</span>
                    </div>
                    <div class="text-gray-800 font-semibold" id="opsCommandCount">--</div>
                </div>
            </div>
        </div>
    </div>



</div>

<!-- Performance Concerns - MySQL-style -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Performance Concerns</h3>
        <div class="flex space-x-2">
            <button id="tabConcernSlow" class="px-4 py-2 btn-primary rounded-lg text-sm">Slow Queries</button>
            <button id="tabConcernWarnings" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Warnings</button>
            <button id="tabConcernErrors" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Errors</button>
        </div>
    </div>
    <div class="text-sm text-gray-600 mb-4" id="concernCounts">Errors: --, Warnings: --</div>
    <div id="concernSlowContent" class="space-y-4">
        <div class="text-center py-8 text-gray-500">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Loading slow queries...</p>
        </div>
    </div>
    <div id="concernWarningsContent" class="space-y-4 hidden">
        <div class="text-center py-8 text-gray-500">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Loading warnings...</p>
        </div>
    </div>
    <div id="concernErrorsContent" class="space-y-4 hidden">
        <div class="text-center py-8 text-gray-500">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Loading errors...</p>
        </div>
    </div>
</div>

@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const serverId = {{ $server->id }};
    const serverIp = "{{ $server->ip }}";
    const panel = "{{ $panel ?? 'admin' }}";
    
    // Initialize charts with empty data
    let opsChart, memoryChart, connectionChart, networkChart;
    let bytesSentChartObj, bytesReceivedChartObj;
    let globalLockChart;
    let opsPieChart;
    let activeConcern = 'slow';
    
    function createChartCanvas(selectorId) {
        const container = document.querySelector(`#${selectorId}`);
        if (!container) return null;
        container.innerHTML = '';
        const canvas = document.createElement('canvas');
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        container.appendChild(canvas);
        return canvas.getContext('2d');
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
    
    function showApiStatus(type, message) {
        const dotWrap = document.getElementById('apiStatusDot');
        if (dotWrap) {
            const dot = dotWrap.querySelector('span') || dotWrap;
            dotWrap.classList.remove('hidden');
            dot.className = `inline-block w-2 h-2 rounded-full ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        }
        showToast(type, type === 'success' ? 'Connected' : (message || 'Connection failed'));
    }
    
    function setGaugeCenter(containerId, percent, label) {
        const container = document.getElementById(containerId);
        if (!container) return;
        let center = container.querySelector('.gauge-center');
        if (!center) {
            center = document.createElement('div');
            center.className = 'gauge-center';
            center.style.position = 'absolute';
            center.style.inset = '0';
            center.style.display = 'flex';
            center.style.flexDirection = 'column';
            center.style.alignItems = 'center';
            center.style.justifyContent = 'center';
            center.style.pointerEvents = 'none';
            center.innerHTML = '<div class="gauge-percent text-2xl font-semibold text-gray-800"></div><div class="gauge-label text-xs text-gray-500"></div>';
            container.style.position = 'relative';
            container.appendChild(center);
        }
        const pctEl = center.querySelector('.gauge-percent');
        const lblEl = center.querySelector('.gauge-label');
        if (pctEl) pctEl.textContent = `${Math.round(percent || 0)}%`;
        if (lblEl) lblEl.textContent = label || '';
    }
    
    function initializeCharts() {
        const opsCtx = createChartCanvas('mongoOpsPerSecond');
        if (opsCtx) {
            opsChart = new Chart(opsCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Ops/Sec',
                        data: [],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });
        }
        
        const memCtx = createChartCanvas('mongoMemoryUsage');
        if (memCtx) {
            memoryChart = new Chart(memCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Memory (MB)',
                        data: [],
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });
        }
        
        const connCtx = createChartCanvas('mongoConnectionUsage');
        if (connCtx) {
            connectionChart = new Chart(connCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [0, 100],
                        backgroundColor: ['rgb(139, 92, 246)', '#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    cutout: '70%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });
            setGaugeCenter('mongoConnectionUsage', 0, 'Usage');
        }
        
        const netCtx = createChartCanvas('networkTrafficChart');
        if (netCtx) {
            networkChart = new Chart(netCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'MB In',
                            data: [],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.15)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'MB Out',
                            data: [],
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });
        }
        
        const sentCtx = createChartCanvas('bytesSentChart');
        if (sentCtx) {
            bytesSentChartObj = new Chart(sentCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Bytes Sent',
                        data: [],
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });
        }
        
        const recvCtx = createChartCanvas('bytesReceivedChart');
        if (recvCtx) {
            bytesReceivedChartObj = new Chart(recvCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Bytes Received',
                        data: [],
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });
        }
        
        const glCtx = createChartCanvas('mongoGlobalLockStatus');
        if (glCtx) {
            globalLockChart = new Chart(glCtx, {
                type: 'bar',
                data: {
                    labels: ['Readers', 'Writers'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#8b5cf6', '#f59e0b'],
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });
        }
        
        const opsPieCtx = createChartCanvas('mongoOpsPieChart');
        if (opsPieCtx) {
            opsPieChart = new Chart(opsPieCtx, {
                type: 'pie',
                data: {
                    labels: ['Query', 'Insert', 'Update', 'Delete', 'Command'],
                    datasets: [{
                        data: [0, 0, 0, 0, 0],
                        backgroundColor: [
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(234, 179, 8)',
                            'rgb(239, 68, 68)',
                            'rgb(99, 102, 241)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    }
    
    // Fetch MongoDB data
    async function fetchMongoDBData() {
        try {
            const response = await fetch(`/${panel}/server/${serverId}/mongodb-data`, {
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
    
    async function fetchMongoConcerns() {
        try {
            const response = await fetch(`/${panel}/server/${serverId}/mongodb-concerns`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            return data;
        } catch (e) {
            return { ok: false, message: 'Failed to fetch performance concerns' };
        }
    }
    
    let activeConcernTab = 'slow';
    function showConcernTab(tabName) {
        activeConcernTab = tabName;
        const btnSlow = document.getElementById('tabConcernSlow');
        const btnWarn = document.getElementById('tabConcernWarnings');
        const btnErr = document.getElementById('tabConcernErrors');
        [btnSlow, btnWarn, btnErr].forEach(btn => {
            if (btn) {
                btn.classList.remove('btn-primary');
                btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            }
        });
        const activeId = 'tabConcern' + tabName.charAt(0).toUpperCase() + tabName.slice(1);
        const activeBtn = document.getElementById(activeId);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            activeBtn.classList.add('btn-primary');
        }
        const slowEl = document.getElementById('concernSlowContent');
        const warnEl = document.getElementById('concernWarningsContent');
        const errEl = document.getElementById('concernErrorsContent');
        [slowEl, warnEl, errEl].forEach(el => { if (el) el.classList.add('hidden'); });
        const targetEl = document.getElementById(`concern${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Content`);
        if (targetEl) targetEl.classList.remove('hidden');
        loadConcernTabContent(tabName);
    }
    
    async function loadConcernTabContent(tabName) {
        const countsEl = document.getElementById('concernCounts');
        const contentEl = document.getElementById(`concern${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Content`);
        if (!contentEl) return;
        if (tabName === 'slow') {
            contentEl.innerHTML = `
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
                    <p class="text-gray-600 font-medium">Loading slow queries...</p>
                </div>
            `;
        } else if (tabName === 'warnings') {
            contentEl.innerHTML = `
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-yellow-200 border-t-yellow-600 mb-4"></div>
                    <p class="text-gray-600 font-medium">Loading warnings...</p>
                </div>
            `;
        } else {
            contentEl.innerHTML = `
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-red-200 border-t-red-600 mb-4"></div>
                    <p class="text-gray-600 font-medium">Loading errors...</p>
                </div>
            `;
        }
        try {
            const data = await fetchMongoConcerns();
            if (countsEl && data && data.counts) {
                countsEl.textContent = `Errors: ${data.counts.errors}, Warnings: ${data.counts.warns}`;
            }
            if (!data || !data.ok) {
                if (tabName === 'slow') {
                    contentEl.innerHTML = `
                        <div class="text-center py-12 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-600 font-medium">No Slow Queries</p>
                            <p class="text-sm text-gray-500 mt-2">Unable to load slow queries</p>
                        </div>
                    `;
                } else if (tabName === 'warnings') {
                    renderDefaultWarnings(contentEl, 'Unable to load warnings');
                } else {
                    renderDefaultErrors(contentEl, 'Unable to load errors');
                }
                return;
            }
            if (tabName === 'slow') {
                renderSlowQueries(contentEl, data.slowQueries || []);
            } else if (tabName === 'warnings') {
                renderWarningsTab(contentEl, data.warnings || []);
            } else {
                renderErrorsTab(contentEl, data.errors || []);
            }
        } catch (e) {
            if (tabName === 'slow') {
                contentEl.innerHTML = `
                    <div class="text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-red-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-600 font-medium">Unable to Load Slow Queries</p>
                        <p class="text-sm text-gray-500 mt-2">Network or server error occurred</p>
                    </div>
                `;
            } else if (tabName === 'warnings') {
                renderDefaultWarnings(contentEl, 'Network error occurred');
            } else {
                renderDefaultErrors(contentEl, 'Network error occurred');
            }
        }
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
            <div class="space-y-4">
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        ${slowQueries.length} slow queries detected
                    </span>
                </div>
        `;
        slowQueries.slice(0, 20).forEach(q => {
            const sql = (q.sql_text || '').toString();
            const qt = q.query_time || '--';
            const st = q.start_time || '--';
            html += `
                <div class="border rounded-lg p-4 bg-yellow-50 border-yellow-100">
                    <div class="text-sm font-medium text-gray-800 break-words">${sql || '--'}</div>
                    <div class="text-xs text-gray-600 mt-1">Query Time: ${qt} • Start: ${st}</div>
                </div>
            `;
        });
        html += `</div>`;
        containerEl.innerHTML = html;
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
                                <p class="text-green-700">All MongoDB metrics are within normal ranges.</p>
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
                        ${warnings.length} warning${warnings.length !== 1 ? 's' : ''} detected
                    </span>
                </div>
        `;
        warnings.forEach(entry => {
            let msg = entry.message || '';
            let ts = entry.timestamp || '';
            try {
                const parsed = typeof msg === 'string' ? JSON.parse(msg) : msg;
                msg = parsed.msg || msg;
                ts = parsed.t?.$date || ts;
            } catch(e) {}
            html += `
                <div class="border rounded-lg p-4 bg-yellow-50 border-yellow-100">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">MONGODB WARNING</h4>
                            <p class="mt-2 text-sm text-gray-700 break-words">${msg}</p>
                            <div class="mt-2 text-xs text-gray-500">${ts || '--'}</div>
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
                                <p class="text-green-700">MongoDB is operating without critical errors.</p>
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
                        ${errors.length} error${errors.length !== 1 ? 's' : ''} detected
                    </span>
                </div>
        `;
        errors.forEach(entry => {
            let msg = entry.message || '';
            let ts = entry.timestamp || '';
            try {
                const parsed = typeof msg === 'string' ? JSON.parse(msg) : msg;
                msg = parsed.msg || msg;
                ts = parsed.t?.$date || ts;
            } catch(e) {}
            html += `
                <div class="border rounded-lg p-4 bg-red-50 border-red-100">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">MONGODB ERROR</h4>
                            <p class="mt-2 text-sm text-gray-700 break-words">${msg}</p>
                            <div class="mt-2 text-xs text-gray-500">${ts || '--'}</div>
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
    
    // Update dashboard with data
    function updateDashboard(data) {
        if (!data || !data.ok) {
            // Show error state (section removed; guard if elements exist)
            const apiStatusEl = document.getElementById('apiStatus');
            const apiMsgEl = document.getElementById('apiMessage');
            if (apiStatusEl) {
                apiStatusEl.className = 'px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                apiStatusEl.textContent = 'Disconnected';
            }
            if (apiMsgEl) {
                apiMsgEl.textContent = data?.message || 'Failed to fetch MongoDB data';
            }
            return;
        }
        
        // Update API status (section removed; guard if elements exist)
        const apiStatusEl = document.getElementById('apiStatus');
        const apiMsgEl = document.getElementById('apiMessage');
        if (apiStatusEl) {
            apiStatusEl.className = 'px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            apiStatusEl.textContent = 'Connected';
        }
        if (apiMsgEl) {
            apiMsgEl.textContent = 'Connected to MongoDB API successfully';
        }
        
        // Update metrics
        const metrics = data.summary || {};
        
        // Top metrics
        document.getElementById('currentConnections').textContent = metrics.connections_current || '0';
        document.getElementById('queriesPerSec').textContent = metrics.queries_per_second || '0';
        document.getElementById('memoryUsage').textContent = metrics.memory_resident || '0';
        document.getElementById('availableConnections').textContent = metrics.connections_available || '0';
        document.getElementById('activeClientsTop').textContent = metrics.connections_current || '0';

        const usagePct = typeof metrics.connections_percent === 'number' ? metrics.connections_percent.toFixed(1) + '%' : '--%';
        const usageRightEl = document.getElementById('connectionsUsageRight');
        if (usageRightEl) usageRightEl.textContent = usagePct;
        const memVirtEl = document.getElementById('memoryVirtual');
        if (memVirtEl) memVirtEl.textContent = (metrics.memory_virtual ?? '--') + (metrics.memory_virtual ? ' MB' : '');
        const activeClientsEl = document.getElementById('activeClientsTop');
        const activeClients = (metrics.global_lock_active_readers || 0) + (metrics.global_lock_active_writers || 0);
        if (activeClientsEl) activeClientsEl.textContent = String(activeClients);
        
        // Connection usage
        document.getElementById('currentConnectionsText').textContent = metrics.connections_current || '0';
        document.getElementById('availableConnectionsText').textContent = metrics.connections_available || '0';
        
        // Operation totals (legacy IDs) - update only if present
        const tq = document.getElementById('totalQueries'); if (tq) tq.textContent = metrics.queries_per_second || '0';
        const ti = document.getElementById('totalInserts'); if (ti) ti.textContent = metrics.inserts_per_second || '0';
        const tu = document.getElementById('totalUpdates'); if (tu) tu.textContent = metrics.updates_per_second || '0';
        const td = document.getElementById('totalDeletes'); if (td) td.textContent = metrics.deletes_per_second || '0';
        const tc = document.getElementById('totalCommands'); if (tc) tc.textContent = metrics.commands_per_second || '0';
        
        // Network traffic
        document.getElementById('bytesIn').textContent = (metrics.network_in || 0) + ' MB';
        document.getElementById('bytesOut').textContent = (metrics.network_out || 0) + ' MB';
        
        if (data.chartData) {
            if (data.chartData.operations && opsChart) {
                opsChart.data.labels = data.chartData.operations.labels || [];
                opsChart.data.datasets[0].data = data.chartData.operations.data || [];
                opsChart.update();
                const peak = (data.chartData.operations.data || []).reduce((m, v) => Math.max(m, Number(v) || 0), 0);
                const peakEl = document.getElementById('queriesPeak');
                if (peakEl) peakEl.textContent = peak ? peak.toFixed(2) : '--';
            }
            if (data.chartData.memory && memoryChart) {
                memoryChart.data.labels = data.chartData.memory.labels || [];
                memoryChart.data.datasets[0].data = data.chartData.memory.data || [];
                memoryChart.update();
            }
            if (metrics.connections_percent !== undefined && connectionChart) {
                const val = metrics.connections_percent || 0;
                connectionChart.data.datasets[0].data = [val, 100 - val];
                connectionChart.update();
                setGaugeCenter('mongoConnectionUsage', val, 'Usage');
            }
            if (data.chartData.network && networkChart) {
                networkChart.data.labels = data.chartData.network.labels || [];
                networkChart.data.datasets[0].data = data.chartData.network.in || [];
                networkChart.data.datasets[1].data = data.chartData.network.out || [];
                networkChart.update();
            }
            if (data.chartData.network && bytesSentChartObj) {
                const labels = data.chartData.network.labels || [];
                const outMb = data.chartData.network.out || [];
                const outKb = (outMb || []).map(v => (Number(v) || 0) * 1024);
                const sentMb = metrics.network_out || 0;
                const sentKb = sentMb * 1024;
                const useMb = sentKb >= 1000;
                bytesSentChartObj.data.labels = labels;
                bytesSentChartObj.data.datasets[0].label = useMb ? 'MB Sent' : 'KB Sent';
                bytesSentChartObj.data.datasets[0].data = useMb ? outMb : outKb;
                bytesSentChartObj.update();
            }
            if (data.chartData.network && bytesReceivedChartObj) {
                const labels = data.chartData.network.labels || [];
                const inMb = data.chartData.network.in || [];
                const inKb = (inMb || []).map(v => (Number(v) || 0) * 1024);
                const recvMb = metrics.network_in || 0;
                const recvKb = recvMb * 1024;
                const useMb = recvKb >= 1000;
                bytesReceivedChartObj.data.labels = labels;
                bytesReceivedChartObj.data.datasets[0].label = useMb ? 'MB Received' : 'KB Received';
                bytesReceivedChartObj.data.datasets[0].data = useMb ? inMb : inKb;
                bytesReceivedChartObj.update();
            }
        }
        
        if (globalLockChart) {
            const readers = metrics.global_lock_readers || 0;
            const writers = metrics.global_lock_writers || 0;
            globalLockChart.data.datasets[0].data = [readers, writers];
            globalLockChart.update();
        }
        document.getElementById('globalActiveReaders').textContent = metrics.global_lock_active_readers || '0';
        document.getElementById('globalActiveWriters').textContent = metrics.global_lock_active_writers || '0';
        const sentLatestEl = document.getElementById('bytesSentLatest');
        const sentUnitEl = document.getElementById('bytesSentUnit');
        const recvLatestEl = document.getElementById('bytesReceivedLatest');
        const recvUnitEl = document.getElementById('bytesReceivedUnit');
        const sentMb = metrics.network_out || 0;
        const sentKb = sentMb * 1024;
        const recvMb = metrics.network_in || 0;
        const recvKb = recvMb * 1024;
        if (sentLatestEl) sentLatestEl.textContent = sentKb < 1000 ? `${Math.round(sentKb)} KB/s` : `${Math.round(sentMb)} MB/s`;
        if (sentUnitEl) sentUnitEl.textContent = sentKb < 1000 ? 'KB/s' : 'MB/s';
        if (recvLatestEl) recvLatestEl.textContent = recvKb < 1000 ? `${Math.round(recvKb)} KB/s` : `${Math.round(recvMb)} MB/s`;
        if (recvUnitEl) recvUnitEl.textContent = recvKb < 1000 ? 'KB/s' : 'MB/s';
        
        if (opsPieChart) {
            const q = Number(metrics.queries_per_second) || 0;
            const i = Number(metrics.inserts_per_second) || 0;
            const u = Number(metrics.updates_per_second) || 0;
            const d = Number(metrics.deletes_per_second) || 0;
            const c = Number(metrics.commands_per_second) || 0;
            opsPieChart.data.datasets[0].data = [q, i, u, d, c];
            opsPieChart.update();
        }
        
        const breakdown = data.chartData?.opsBreakdown || null;
        if (breakdown) {
            const sum = arr => (arr || []).reduce((m, v) => m + (Number(v) || 0), 0);
            const setCount = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = String(val); };
            setCount('opsQueryCount', Math.round(sum(breakdown.query?.data)));
            setCount('opsInsertCount', Math.round(sum(breakdown.insert?.data)));
            setCount('opsUpdateCount', Math.round(sum(breakdown.update?.data)));
            setCount('opsDeleteCount', Math.round(sum(breakdown.delete?.data)));
            setCount('opsCommandCount', Math.round(sum(breakdown.command?.data)));
        } else {
            const setCount = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = (Number(val) || 0).toFixed(2); };
            setCount('opsQueryCount', metrics.queries_per_second);
            setCount('opsInsertCount', metrics.inserts_per_second);
            setCount('opsUpdateCount', metrics.updates_per_second);
            setCount('opsDeleteCount', metrics.deletes_per_second);
            setCount('opsCommandCount', metrics.commands_per_second);
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
            if (data && data.ok) {
                updateDashboard(data);
                showApiStatus('success', 'API connected successfully');
            } else {
                updateDashboard(data);
                const msg = data && data.message ? data.message : 'Failed to fetch MongoDB data';
                showApiStatus('error', msg);
            }
        } catch (error) {
            console.error('Refresh failed:', error);
            showApiStatus('error', 'Network error - cannot connect to API');
        } finally {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            `;
        }
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        refreshData();
        const btnSlow = document.getElementById('tabConcernSlow');
        const btnWarn = document.getElementById('tabConcernWarnings');
        const btnErr = document.getElementById('tabConcernErrors');
        btnSlow.addEventListener('click', () => showConcernTab('slow'));
        btnWarn.addEventListener('click', () => showConcernTab('warnings'));
        btnErr.addEventListener('click', () => showConcernTab('errors'));
        showConcernTab('slow');
        
        document.getElementById('refreshBtn').addEventListener('click', refreshData);
        setInterval(refreshData, 30000);
    });
</script>
@endpush
