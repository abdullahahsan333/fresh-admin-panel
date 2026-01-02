@extends($layout ?? 'layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Redis</h1>
        <div id="apiStatusDot" class="hidden">
            <span class="inline-block w-2 h-2 rounded-full"></span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <button id="refreshData" class="text-gray-500 hover:text-gray-700 p-1 rounded hover:bg-gray-100" title="Refresh data">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
            </svg>
        </button>
        <div class="text-sm text-gray-500">Last updated: <span id="lastUpdated">{{ now()->format('M d, H:i') }}</span></div>
    </div>
</header>

<!-- Top Row: 4 Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Connected Clients -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-[rgb(var(--color-primary)/.12)] flex items-center justify-center text-[rgb(var(--color-primary))]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <span id="connectedClients" class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Connected Clients</div>
        <div class="text-xs text-gray-500">Active Redis connections</div>
    </div>

    <!-- Ops / Sec -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-yellow-100 flex items-center justify-center text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span id="opsPerSec" class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Ops / Sec</div>
        <div class="text-xs text-gray-500">Instantaneous operations</div>
    </div>

    <!-- Used Memory -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-red-100 flex items-center justify-center text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <span id="usedMemory" class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Used Memory</div>
        <div class="text-xs text-gray-500">Current memory usage</div>
    </div>

    <!-- Total Keys -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <span id="totalKeys" class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Total Keys</div>
        <div class="text-xs text-gray-500">Keys in database</div>
    </div>
</div>

<!-- Second Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Operations Per Second -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Operations Per Second</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div id="redisOpsPerSecond" class="h-60 w-full"></div>
        <div class="w-full flex justify-between px-4 text-xs mt-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded bg-[rgb(var(--color-primary))]"></span>
                <div>
                    <div class="font-medium text-gray-700">Ops / Second</div>
                    <div id="opsSummary" class="text-gray-500">Latest: --, Avg: --</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memory Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Memory Usage</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="flex-1 flex flex-col items-center justify-center">
            <div id="redisMemoryUsage" class="h-36 w-full mb-4"></div>
            <div class="w-full flex justify-between px-4 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-[rgb(var(--color-primary)/.2)]"></span>
                    <div>
                        <div class="font-medium text-gray-700">Used Memory</div>
                        <div id="usedMemoryValue" class="text-gray-500">0B</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-blue-200"></span>
                    <div>
                        <div class="font-medium text-gray-700">Total System</div>
                        <div id="totalMemoryValue" class="text-gray-500">0B</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commands vs Memory -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Commands vs Memory</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div id="redisCommandsChart" class="h-60 w-full"></div>
        <div class="w-full flex justify-between px-4 text-xs mt-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded bg-[rgb(var(--color-primary))]"></span>
                <div>
                    <div class="font-medium text-gray-700">Ops / Second</div>
                    <div id="commandsOpsSummary" class="text-gray-500">Latest: --, Avg: --</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded" style="background-color: rgb(37, 99, 235)"></span>
                <div>
                    <div class="font-medium text-gray-700">Memory (MB)</div>
                    <div id="commandsMemSummary" class="text-gray-500">Latest: --, Avg: --</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Third Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Redis Command Overview -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Redis Command Overview</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div id="redisCommandsPieChart" class="h-48 w-full mb-4 flex justify-center"></div>

        <div class="grid grid-cols-4 gap-2 mb-4 text-center">
            <!-- GET -->
            <div class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition-colors">
                <span class="text-xs font-bold text-purple-600 mb-1">GET</span>
                <span id="getCommands" class="text-lg font-bold text-gray-800">0</span>
            </div>
            
            <!-- SET -->
            <div class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition-colors">
                <span class="text-xs font-bold text-green-600 mb-1">SET</span>
                <span id="setCommands" class="text-lg font-bold text-gray-800">0</span>
            </div>
            
            <!-- HGET -->
            <div class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition-colors">
                <span class="text-xs font-bold text-blue-600 mb-1">HGET</span>
                <span id="hgetCommands" class="text-lg font-bold text-gray-800">0</span>
            </div>
            
            <!-- HSET -->
            <div class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition-colors">
                <span class="text-xs font-bold text-yellow-600 mb-1">HSET</span>
                <span id="hsetCommands" class="text-lg font-bold text-gray-800">0</span>
            </div>
        </div>
        
        <!-- Additional commands - dynamically shown if data exists -->
        <div id="additionalCommands" class="grid grid-cols-4 gap-2 mb-4 text-center empty:hidden"></div>
        
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>Total Commands: <span id="totalCommands">0</span></span>
                <span>Updated: <span id="commandsUpdated">Just now</span></span>
            </div>
        </div>
    </div>

    <!-- Cache Hit Ratio -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Cache Hit Ratio</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="flex-1 flex flex-col items-center justify-center">
            <div id="redisCacheHitRatio" class="h-36 w-full mb-4"></div>
            <div class="w-full flex justify-between px-4 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-green-200"></span>
                    <div>
                        <div class="font-medium text-gray-700">Key Hits</div>
                        <div id="keyHits" class="text-gray-500">0</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-red-200"></span>
                    <div>
                        <div class="font-medium text-gray-700">Key Misses</div>
                        <div id="keyMisses" class="text-gray-500">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Traffic -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Network Traffic</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div id="redisNetworkTraffic" class="h-36 w-full mb-6"></div>

        <div class="flex flex-col mt-auto">
            <div class="text-sm text-gray-600 mb-2">Current Speed</div>
            <div class="flex justify-between items-end">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-3 h-3 rounded bg-purple-500"></span>
                        <div>
                            <div class="text-xs text-gray-500">Input KB/s</div>
                            <div id="currentNetworkInput" class="text-lg font-medium text-gray-800">0</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-3 h-3 rounded bg-blue-500"></span>
                        <div>
                            <div class="text-xs text-gray-500">Output KB/s</div>
                            <div id="currentNetworkOutput" class="text-lg font-medium text-gray-800">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fourth Row: Bottom Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Connected Slaves -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-purple-100 flex items-center justify-center text-purple-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <span id="connectedSlaves" class="text-xl font-semibold text-gray-800">0</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Connected Slaves</div>
        <div class="text-xs text-gray-500">Replica nodes connected</div>
    </div>
    
    <!-- Blocked Clients -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-red-100 flex items-center justify-center text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.293 4.293a1 1 0 011.414 0L12 10.586l6.293-6.293a1 1 0 111.414 1.414L13.414 12l6.293 6.293a1 1 0 01-1.414 1.414L12 13.414l-6.293 6.293a1 1 0 01-1.414-1.414L10.586 12 4.293 5.707a1 1 0 010-1.414z" />
                </svg>
            </div>
            <span id="blockedClients" class="text-xl font-semibold text-gray-800">0</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Blocked Clients</div>
        <div class="text-xs text-gray-500">Clients currently blocked</div>
    </div>

    <!-- Expired Keys -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-yellow-100 flex items-center justify-center text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-7a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span id="expiredKeys" class="text-xl font-semibold text-gray-800">0</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Expired Keys</div>
        <div class="text-xs text-gray-500">Keys expired</div>
    </div>

    <!-- Evicted Keys -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-7 7-3-3m-4 3h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <span id="evictedKeys" class="text-xl font-semibold text-gray-800">0</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Evicted Keys</div>
        <div class="text-xs text-gray-500">Keys evicted</div>
    </div>
</div>

<!-- Loading overlay removed: data loads in background with toast + header dot -->

@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Global variables
    const panel = "{{ $panel ?? 'admin' }}";
    let serverIp = '';
    let serverId = '';
    let autoRefreshInterval = null;
    let charts = {};
    const chartColors = {
        primary: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim()
    };
    const themeRgb = chartColors.primary.replace(/\s+/g, ',');
    const themeColor = `rgb(${themeRgb})`;
    const themeFill = `rgba(${themeRgb}, 0.15)`;
    const accentBlue = 'rgb(59, 130, 246)';
    const accentBlueFill = 'rgba(59, 130, 246, 0.15)';
    const accentGreen = 'rgb(16, 185, 129)';
    const accentGreenFill = 'rgba(16, 185, 129, 0.15)';
    let netLabels = [];
    let netInputRates = [];
    let netOutputRates = [];
    serverId = {{ $server->id }};
    serverIp = @json($server->ip);

    // Helper functions
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0 || bytes === undefined || bytes === null) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Format numbers for display
    function formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toString();
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function setChartLoading(containerId, isLoading) { return; }

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

    function createChartCanvas(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return null;
        container.innerHTML = '';
        const canvas = document.createElement('canvas');
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        container.appendChild(canvas);
        return canvas.getContext('2d');
    }

    function updateLastUpdated() {
        const now = new Date();
        const formatted = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' + 
                         now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
        document.getElementById('lastUpdated').textContent = formatted;
    }

    function addNetworkSample(inputKBps, outputKBps) {
        const label = new Date().toLocaleTimeString('en-US', { 
            hour12: false, 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        
        netLabels.push(label);
        netInputRates.push(Math.round(inputKBps || 0));
        netOutputRates.push(Math.round(outputKBps || 0));
        
        // Keep only last 10 data points
        if (netLabels.length > 10) netLabels = netLabels.slice(-10);
        if (netInputRates.length > 10) netInputRates = netInputRates.slice(-10);
        if (netOutputRates.length > 10) netOutputRates = netOutputRates.slice(-10);
    }

    // Function to render command pie chart
    function renderCommandProgress(stats) {
        if (!charts.commandsPie) return;
        
        // Extract command counts
        const get = Number(stats?.GET) || 0;
        const set = Number(stats?.SET) || 0;
        const hget = Number(stats?.HGET) || 0;
        const hset = Number(stats?.HSET) || 0;
        
        // Update Chart
        charts.commandsPie.data.datasets[0].data = [get, set, hget, hset];
        charts.commandsPie.update();
    }

    // Add this function to format command counts
    function formatCommandCount(count) {
        if (count >= 1000000) {
            return (count / 1000000).toFixed(1) + 'M';
        }
        if (count >= 1000) {
            return (count / 1000).toFixed(1) + 'K';
        }
        return count.toString();
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
    function updateApiStatus(connected, message = '') {
        const dotWrap = document.getElementById('apiStatusDot');
        if (dotWrap) {
            const dot = dotWrap.querySelector('span') || dotWrap;
            dotWrap.classList.remove('hidden');
            dot.className = `inline-block w-2 h-2 rounded-full ${connected ? 'bg-green-500' : 'bg-red-500'}`;
        }
        showToast(connected ? 'success' : 'error', connected ? 'Connected' : (message || 'Connection failed'));
    }

    function showLoading(show) { return; }
    async function fetchJsonWithRetry(url, options = {}, retries = 2, backoffMs = 800) {
        for (let attempt = 0; attempt <= retries; attempt++) {
            try {
                const res = await fetch(url, options);
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const ct = res.headers.get('content-type') || '';
                if (!ct.includes('application/json')) throw new Error('Invalid content-type');
                return await res.json();
            } catch (e) {
                if (attempt === retries) throw e;
                await new Promise(r => setTimeout(r, backoffMs * (attempt + 1)));
            }
        }
    }

    // Chart initialization
    function initializeCharts() {
        const opsCtx = createChartCanvas('redisOpsPerSecond');
        if (opsCtx) {
            const chart = new Chart(opsCtx, {
                type: 'line',
                devicePixelRatio: 1,
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Ops/Sec',
                        data: [],
                        borderColor: themeColor,
                        backgroundColor: themeFill,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                        legend: { display: false }
                        },
                        scales: {
                            x: { display: false },
                            y: { 
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            }
                        }
                    }
                });
            charts.ops = chart;
            setChartLoading('redisOpsPerSecond', true);
        }

        const memoryCtx = createChartCanvas('redisMemoryUsage');
        if (memoryCtx) {
            const chart = new Chart(memoryCtx, {
                type: 'doughnut',
                devicePixelRatio: 1,
                data: {
                    datasets: [{
                        data: [0, 100],
                        backgroundColor: [accentGreen, '#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
            charts.memory = chart;
            setChartLoading('redisMemoryUsage', true);
            setGaugeCenter('redisMemoryUsage', 0, 'Usage');
        }

        const commandsCtx = createChartCanvas('redisCommandsChart');
        if (commandsCtx) {
            const chart = new Chart(commandsCtx, {
                type: 'line',
                devicePixelRatio: 1,
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Ops/Second',
                            data: [],
                            borderColor: accentGreen,
                            backgroundColor: accentGreenFill,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Memory (Scaled)',
                            data: [],
                            borderColor: accentBlue,
                            backgroundColor: accentBlueFill,
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
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        x: { 
                            grid: { display: true, color: 'rgba(0,0,0,0.06)' },
                            title: { display: true, text: 'Time' }
                        },
                        y: { 
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.06)' },
                            title: { display: true, text: 'Combined Metric' },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
            charts.commands = chart;
            setChartLoading('redisCommandsChart', true);
        }

        const cacheCtx = createChartCanvas('redisCacheHitRatio');
        if (cacheCtx) {
            const chart = new Chart(cacheCtx, {
                type: 'doughnut',
                devicePixelRatio: 1,
                data: {
                    datasets: [{
                        data: [0, 100],
                        backgroundColor: [themeColor, '#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false },
                        tooltip: { enabled: false }
                        }
                    }
                });
                charts.cache = chart;
                setChartLoading('redisCacheHitRatio', true);
                setGaugeCenter('redisCacheHitRatio', 0, 'Hit Ratio');
        }

        const pieCtx = createChartCanvas('redisCommandsPieChart');
        if (pieCtx) {
            const chart = new Chart(pieCtx, {
                type: 'pie',
                devicePixelRatio: 1,
                data: {
                    labels: ['GET', 'SET', 'HGET', 'HSET'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            'rgb(168, 85, 247)', // Purple-500
                            'rgb(34, 197, 94)',  // Green-500
                            'rgb(59, 130, 246)', // Blue-500
                            'rgb(234, 179, 8)'   // Yellow-500
                        ],
                        borderWidth: 1,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: { 
                            position: 'right',
                            labels: { boxWidth: 12, usePointStyle: true }
                        },
                        tooltip: { 
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.chart._metasets[context.datasetIndex].total;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) + '%' : '0%';
                                    return label + ': ' + value + ' (' + percentage + ')';
                                }
                            }
                        }
                    }
                }
            });
            charts.commandsPie = chart;
        }

        const networkCtx = createChartCanvas('redisNetworkTraffic');
        if (networkCtx) {
            const chart = new Chart(networkCtx, {
                type: 'line',
                devicePixelRatio: 1,
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Input KB/s',
                            data: [],
                            borderColor: 'rgb(147, 51, 234)', // Purple color
                            backgroundColor: 'rgba(147, 51, 234, 0.15)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Output KB/s',
                            data: [],
                            borderColor: 'rgb(59, 130, 246)', // Blue color
                            backgroundColor: 'rgba(59, 130, 246, 0.15)',
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
                        plugins: {
                        legend: { display: false }
                        },
                        scales: {
                            x: { display: false },
                            y: { 
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                title: { display: true, text: 'KB/s' }
                            }
                        }
                    }
                });
                charts.network = chart;
            setChartLoading('redisNetworkTraffic', true);
        }
    }

    // Update charts with data
    function updateCharts(data) {
        // Update Ops per second chart
        if (charts.ops && data.opsData && data.opsData.labels) {
            const labels = (data.opsData.labels || []).slice(-10);
            const values = (data.opsData.values || []).slice(-10);
            const len = Math.min(labels.length, values.length);
            charts.ops.data.labels = labels.slice(labels.length - len);
            charts.ops.data.datasets[0].data = values.slice(values.length - len);
            charts.ops.update();
            if (len > 0) setChartLoading('redisOpsPerSecond', false);
            const latest = len ? values[values.length - 1] : 0;
            const avg = len ? (values.reduce((a, b) => a + (Number(b) || 0), 0) / len) : 0;
            setText('opsSummary', 'Latest: ' + (Number(latest).toFixed(0)) + ', Avg: ' + (avg.toFixed(1)));
        }

        // Update Memory usage gauge
        if (charts.memory && data.memoryPercent !== undefined) {
            const pct = Math.max(0, Math.min(100, Number(data.memoryPercent) || 0));
            charts.memory.data.datasets[0].data = [pct, 100 - pct];
            charts.memory.update();
            setChartLoading('redisMemoryUsage', false);
            setGaugeCenter('redisMemoryUsage', pct, 'Usage');
        }

        // Update Commands vs Memory chart
        if (charts.commands && data.opsData && data.opsData.labels && data.memoryData && data.memoryData.values) {
            const labels = (data.opsData.labels || []).slice(-10);
            const ops = (data.opsData.values || []).slice(-10);
            const mem = (data.memoryData.values || []).slice(-10);
            const len = Math.min(labels.length, ops.length, mem.length);
            const opsWindow = ops.slice(ops.length - len).map(v => Number(v) || 0);
            const memWindow = mem.slice(mem.length - len).map(v => Number(v) || 0);
            const opsMax = Math.max(1, ...opsWindow);
            const memMax = Math.max(1, ...memWindow);
            const scale = memMax > 0 ? (opsMax / memMax) : 1;
            const memScaled = memWindow.map(v => Number((v * scale).toFixed(2)));
            charts.commands.data.labels = labels.slice(labels.length - len);
            charts.commands.data.datasets[0].data = opsWindow;
            charts.commands.data.datasets[1].data = memScaled;
            charts.commands.update();
            if (len > 0) setChartLoading('redisCommandsChart', false);
            const latestOps = len ? ops[ops.length - 1] : 0;
            const avgOps = len ? (ops.reduce((a, b) => a + (Number(b) || 0), 0) / len) : 0;
            const latestMem = len ? mem[mem.length - 1] : 0;
            const avgMem = len ? (mem.reduce((a, b) => a + (Number(b) || 0), 0) / len) : 0;
            setText('commandsOpsSummary', 'Latest: ' + (Number(latestOps).toFixed(0)) + ', Avg: ' + (avgOps.toFixed(1)));
            setText('commandsMemSummary', 'Latest: ' + (Number(latestMem).toFixed(2)) + ' MB, Avg: ' + (avgMem.toFixed(2)) + ' MB');
        }

        // Update Cache hit ratio gauge
        if (charts.cache && data.hitRate !== undefined) {
            charts.cache.data.datasets[0].data = [data.hitRate, 100 - data.hitRate];
            charts.cache.update();
            setChartLoading('redisCacheHitRatio', false);
            setGaugeCenter('redisCacheHitRatio', data.hitRate, 'Hit Ratio');
        }

        // Update Network traffic chart
        if (charts.network) {
            charts.network.data.labels = netLabels;
            charts.network.data.datasets[0].data = netInputRates;
            charts.network.data.datasets[1].data = netOutputRates;
            charts.network.update();
            if (netLabels.length > 0) setChartLoading('redisNetworkTraffic', false);
        }
    }

    // Update UI with data
    function updateUI(data) {
        // Update top metrics
        setText('connectedClients', data.connected_clients || '0');
        setText('opsPerSec', data.instantaneous_ops_per_sec || '0');
        setText('usedMemory', formatBytes(data.used_memory || 0));
        setText('totalKeys', data.total_keys || '0');

        // Update memory values
        setText('usedMemoryValue', formatBytes(data.used_memory || 0));
        setText('totalMemoryValue', formatBytes(data.total_system_memory || 0));

        // Update command statistics
        const commandStats = data.commandStats || {};
        setText('getCommands', formatNumber(commandStats.GET || 0));
        setText('setCommands', formatNumber(commandStats.SET || 0));
        setText('hgetCommands', formatNumber(commandStats.HGET || 0));
        setText('hsetCommands', formatNumber(commandStats.HSET || 0));
        
        // Update cache stats
        setText('keyHits', data.keyspace_hits || '0');
        setText('keyMisses', data.keyspace_misses || '0');

        // Update network speeds
        setText('currentNetworkInput', data.instantaneous_input_kbps?.toFixed(2) || '0.00');
        setText('currentNetworkOutput', data.instantaneous_output_kbps?.toFixed(2) || '0.00');

        // Update bottom metrics
        setText('connectedSlaves', data.connected_slaves || '0');
        setText('blockedClients', data.blocked_clients || '0');
        setText('expiredKeys', data.expired_keys || '0');
        setText('evictedKeys', data.evicted_keys || '0');

        // Update server info
        setText('serverIp', serverIp || '--');
        setText('redisUptime', data.uptime || '--');
        setText('redisVersion', data.redis_version || '--');
    }

    // Function to display additional commands
    function renderAdditionalCommands(commandStats) {
        const container = document.getElementById('additionalCommands');
        if (!container) return;
        
        // Filter out the main 4 commands we already display
        const mainCommands = ['GET', 'SET', 'HGET', 'HSET'];
        const additionalCommands = {};
        
        // Extract additional commands with significant usage
        for (const [cmd, value] of Object.entries(commandStats)) {
            const cmdUpper = cmd.toUpperCase();
            if (!mainCommands.includes(cmdUpper) && value > 0) {
                additionalCommands[cmdUpper] = value;
            }
        }
        
        // Sort by value (descending)
        const sortedCommands = Object.entries(additionalCommands)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 4); // Show top 4 additional commands
        
        let html = '';
        if (sortedCommands.length > 0) {
            sortedCommands.forEach(([cmd, value]) => {
                const colorClass = getCommandColor(cmd);
                
                html += `
                    <div class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition-colors">
                        <span class="text-xs font-bold ${colorClass} mb-1">${cmd}</span>
                        <span class="text-lg font-bold text-gray-800">${formatNumber(value)}</span>
                    </div>
                `;
            });
        }
        container.innerHTML = html;
        
        // Update total commands (sum of ALL commands in stats)
        let total = 0;
        for (const val of Object.values(commandStats)) {
            total += Number(val) || 0;
        }
        setText('totalCommands', formatNumber(total));
        setText('commandsUpdated', new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
    }

    // Helper function to get command color
    function getCommandColor(command) {
        const colors = {
            'DEL': 'text-red-500',
            'INCR': 'text-indigo-500',
            'DECR': 'text-indigo-400',
            'LPUSH': 'text-pink-500',
            'RPUSH': 'text-pink-400',
            'LLEN': 'text-teal-500',
            'SADD': 'text-orange-500',
            'ZADD': 'text-amber-500',
            'EXPIRE': 'text-gray-500',
            'TTL': 'text-gray-400'
        };
        return colors[command] || 'text-gray-400';
    }

    // Helper function to get command icon
    function getCommandIcon(command) {
        const icons = {
            'DEL': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>',
            'INCR': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>',
            'DECR': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" /></svg>',
            'LPUSH': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>',
            'RPUSH': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V20m0 0l4-4m-4 4l-4-4M7 4v16" /></svg>',
            'LLEN': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>',
            'SADD': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'ZADD': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>'
        };
        return icons[command] || '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
    }

    // Fetch data from Laravel backend
    async function fetchRedisData() {
        try {
            showLoading(true);
            
            if (!serverId || isNaN(serverId)) {
                throw new Error('Invalid server ID');
            }

            const result = await fetchJsonWithRetry(`/${panel}/server/${serverId}/redis-data`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });
            if (!result.ok) {
                updateApiStatus(false, result.message || 'API error');
                showLoading(false);
                renderCommandProgress({ GET: 0, SET: 0, HGET: 0, HSET: 0 });
                return;
            }

            // Update API status
            updateApiStatus(true);
            
            // Extract data from the API response
            const redisData = result.summary || {};
            const chartData = result.chartData || {};
            serverIp = result.ip || serverIp;
            
            // Prepare command statistics
            const commandStats = redisData.commandStats || {};
            // Populate command stats from flat keys if commandStats is empty or missing specific keys
            if (redisData.commandstats_get !== undefined) commandStats.GET = redisData.commandstats_get;
            if (redisData.commandstats_set !== undefined) commandStats.SET = redisData.commandstats_set;
            if (redisData.commandstats_hget !== undefined) commandStats.HGET = redisData.commandstats_hget;
            if (redisData.commandstats_hset !== undefined) commandStats.HSET = redisData.commandstats_hset;
            
            // Prepare UI data
            const uiData = {
                connected_clients: redisData.connected_clients || 0,
                instantaneous_ops_per_sec: redisData.instantaneous_ops_per_sec || 0,
                used_memory: redisData.used_memory ? redisData.used_memory * 1024 * 1024 : 0, // Convert MB to bytes
                total_system_memory: redisData.total_system_memory ? redisData.total_system_memory * 1024 * 1024 : 0,
                total_keys: redisData.total_keys || 0,
                keyspace_hits: redisData.keyspace_hits || 0,
                keyspace_misses: redisData.keyspace_misses || 0,
                hit_rate: redisData.hit_rate || 0,
                connected_slaves: redisData.connected_slaves || 0,
                blocked_clients: redisData.blocked_clients || 0,
                expired_keys: redisData.expired_keys || 0,
                evicted_keys: redisData.evicted_keys || 0,
                instantaneous_input_kbps: redisData.instantaneous_input_kbps || 0,
                instantaneous_output_kbps: redisData.instantaneous_output_kbps || 0,
                uptime: redisData.uptime_in_seconds ? 
                    Math.floor(redisData.uptime_in_seconds / 86400) + ' days' : '--',
                redis_version: redisData.redis_version || '--',
                commandStats: commandStats
            };

            // Calculate hit rate if not provided
            if (uiData.hit_rate === 0 && uiData.keyspace_hits > 0) {
                const total = uiData.keyspace_hits + uiData.keyspace_misses;
                uiData.hit_rate = total > 0 ? (uiData.keyspace_hits / total * 100) : 0;
            }

            // Update UI with command statistics
            updateUI(uiData);
            
            // Render command progress with actual data
            renderCommandProgress(uiData.commandStats);
            
            // Render additional commands
            renderAdditionalCommands(uiData.commandStats);

            // Add network samples
            addNetworkSample(uiData.instantaneous_input_kbps, uiData.instantaneous_output_kbps);
            
            // Prepare chart update data
            const chartUpdateData = {
                opsData: {
                    labels: chartData.ops?.labels || [],
                    values: chartData.ops?.data || []
                },
                memoryPercent: (function() {
                    const total = uiData.total_system_memory || 0;
                    const used = uiData.used_memory || 0;
                    if (!total || total <= 0) return 0;
                    const pct = (used / total) * 100;
                    return Math.max(0, Math.min(100, Number(pct.toFixed(1))));
                })(),
                commandStats: uiData.commandStats,
                memoryData: {
                    labels: chartData.memory?.labels || [],
                    values: chartData.memory?.data || []
                },
                hitRate: uiData.hit_rate,
                networkData: {
                    labels: chartData.network?.labels || [],
                    input: chartData.network?.input || [],
                    output: chartData.network?.output || []
                }
            };

            updateCharts(chartUpdateData);
            updateLastUpdated();

        } catch (error) {
            console.error('Failed to fetch Redis data:', error);
            updateApiStatus(false, 'Connection failed');
            
            const sampleMetrics = {
                redis_version: "6.0.16",
                connected_clients: 61,
                blocked_clients: 1,
                used_memory: 342837760,
                used_memory_rss: 380272640,
                mem_fragmentation_ratio: 1.11,
                instantaneous_ops_per_sec: 0,
                total_net_input_bytes: 161104656534,
                total_net_output_bytes: 244130943809,
                instantaneous_input_kbps: 0.06,
                instantaneous_output_kbps: 0,
                rejected_connections: 0,
                expired_keys: 3335297,
                evicted_keys: 0,
                keyspace_hits: 500081292,
                keyspace_misses: 4110700,
                connected_slaves: 0,
                uptime_in_seconds: 6571650,
                db0: "keys=233148,expires=16168,avg_ttl=41471269",
                total_keys: 233148,
                total_system_memory: 4101951488
            };
            
            const redisData = {
                connected_clients: sampleMetrics.connected_clients,
                instantaneous_ops_per_sec: sampleMetrics.instantaneous_ops_per_sec,
                used_memory: Math.round(sampleMetrics.used_memory / 1024 / 1024), // MB
                total_system_memory: Math.round(sampleMetrics.total_system_memory / 1024 / 1024), // MB
                total_keys: sampleMetrics.total_keys,
                keyspace_hits: sampleMetrics.keyspace_hits,
                keyspace_misses: sampleMetrics.keyspace_misses,
                hit_rate: (function() {
                    const total = (sampleMetrics.keyspace_hits || 0) + (sampleMetrics.keyspace_misses || 0);
                    return total > 0 ? (sampleMetrics.keyspace_hits / total * 100) : 0;
                })(),
                connected_slaves: sampleMetrics.connected_slaves,
                blocked_clients: sampleMetrics.blocked_clients,
                expired_keys: sampleMetrics.expired_keys,
                evicted_keys: sampleMetrics.evicted_keys,
                instantaneous_input_kbps: sampleMetrics.instantaneous_input_kbps,
                instantaneous_output_kbps: sampleMetrics.instantaneous_output_kbps,
                uptime_in_seconds: sampleMetrics.uptime_in_seconds,
                redis_version: sampleMetrics.redis_version,
                commandStats: {}
            };
            
            const nowLabels = Array.from({length: 10}, (_, i) => {
                const d = new Date(Date.now() - (9 - i) * 1000);
                return d.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            });
            const chartData = {
                ops: { labels: nowLabels, data: Array(10).fill(sampleMetrics.instantaneous_ops_per_sec || 0) },
                memory: { labels: nowLabels, data: Array(10).fill(Number((sampleMetrics.used_memory / 1024 / 1024).toFixed(2))) },
                network: { labels: nowLabels, input: [], output: [] }
            };
            
            serverIp = serverIp || '—';
            
            const uiData = {
                connected_clients: redisData.connected_clients || 0,
                instantaneous_ops_per_sec: redisData.instantaneous_ops_per_sec || 0,
                used_memory: redisData.used_memory ? redisData.used_memory * 1024 * 1024 : 0,
                total_system_memory: redisData.total_system_memory ? redisData.total_system_memory * 1024 * 1024 : 0,
                total_keys: redisData.total_keys || 0,
                keyspace_hits: redisData.keyspace_hits || 0,
                keyspace_misses: redisData.keyspace_misses || 0,
                hit_rate: redisData.hit_rate || 0,
                connected_slaves: redisData.connected_slaves || 0,
                blocked_clients: redisData.blocked_clients || 0,
                expired_keys: redisData.expired_keys || 0,
                evicted_keys: redisData.evicted_keys || 0,
                instantaneous_input_kbps: redisData.instantaneous_input_kbps || 0,
                instantaneous_output_kbps: redisData.instantaneous_output_kbps || 0,
                uptime: redisData.uptime_in_seconds ? Math.floor(redisData.uptime_in_seconds / 86400) + ' days' : '--',
                redis_version: redisData.redis_version || '--',
                commandStats: {}
            };
            
            updateUI(uiData);
            renderCommandProgress(uiData.commandStats);
            renderAdditionalCommands(uiData.commandStats);
            addNetworkSample(uiData.instantaneous_input_kbps, uiData.instantaneous_output_kbps);
            
            const chartUpdateData = {
                opsData: { labels: chartData.ops.labels, values: chartData.ops.data },
                memoryPercent: (function() {
                    const total = uiData.total_system_memory || 0;
                    const used = uiData.used_memory || 0;
                    if (!total || total <= 0) return 0;
                    const pct = (used / total) * 100;
                    return Math.max(0, Math.min(100, Number(pct.toFixed(1))));
                })(),
                commandStats: uiData.commandStats,
                memoryData: { labels: chartData.memory.labels, values: chartData.memory.data },
                hitRate: uiData.hit_rate,
                networkData: { labels: chartData.network.labels, input: chartData.network.input, output: chartData.network.output }
            };
            
            updateCharts(chartUpdateData);
            updateLastUpdated();
            
        } finally {
            showLoading(false);
        }
    }

    // Start auto-refresh
    function startAutoRefresh(interval = 30000) {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        autoRefreshInterval = setInterval(fetchRedisData, interval);
    }

    // Stop auto-refresh
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
    }

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        initializeCharts();
        
        // Set up refresh button
        document.getElementById('refreshData').addEventListener('click', function() {
            fetchRedisData();
        });
        
        // Initial data fetch
        renderCommandProgress({ GET: 0, SET: 0, HGET: 0, HSET: 0 });
        fetchRedisData();
        
        // Start auto-refresh every 30 seconds
        startAutoRefresh(30000);
        
        // Stop auto-refresh when page is hidden
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh(30000);
                fetchRedisData(); // Refresh immediately when coming back
            }
        });
    });
</script>
@endpush
