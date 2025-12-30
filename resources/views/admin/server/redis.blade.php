@extends('layouts.admin')

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
        
        <div class="flex gap-2 mb-4">
            <span class="px-3 py-1 text-xs font-bold text-white bg-[rgb(var(--color-primary))] rounded-sm">GET</span>
            <span class="px-3 py-1 text-xs font-bold text-white bg-green-500 rounded-sm">SET</span>
            <span class="px-3 py-1 text-xs font-bold text-white bg-blue-500 rounded-sm">HGET</span>
            <span class="px-3 py-1 text-xs font-bold text-white bg-yellow-500 rounded-sm">HSET</span>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    GET Commands
                </span>
                <span id="getCommands" class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    SET Commands
                </span>
                <span id="setCommands" class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    HGET Commands
                </span>
                <span id="hgetCommands" class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    HSET Commands
                </span>
                <span id="hsetCommands" class="font-medium text-gray-800">0</span>
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

        <div class="flex justify-between items-end mt-auto">
            <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-8 h-8 rounded bg-purple-100"></span>
                            <div>
                                <div class="text-xs text-gray-500">Input MB/s</div>
                                <div id="networkInput" class="text-lg font-medium text-gray-800">0</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-8 h-8 rounded bg-blue-100"></span>
                            <div>
                                <div class="text-xs text-gray-500">Output MB/s</div>
                                <div id="networkOutput" class="text-lg font-medium text-gray-800">0</div>
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

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-white bg-opacity-30 pointer-events-none z-50 flex items-center justify-center hidden">
    <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[rgb(var(--color-primary))]"></div>
        <p class="mt-4 text-gray-600">Loading Redis data...</p>
    </div>
</div>

@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Global variables
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

    function formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toString();
    }
    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function setChartLoading(containerId, isLoading) {
        const container = document.getElementById(containerId);
        if (!container) return;
        let loader = container.querySelector('.chart-loader');
        if (!loader && isLoading) {
            loader = document.createElement('div');
            loader.className = 'chart-loader flex items-center justify-center text-sm text-gray-600';
            loader.style.position = 'absolute';
            loader.style.inset = '0';
            loader.style.background = 'rgba(255,255,255,0.6)';
            loader.style.zIndex = '10';
            loader.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-[rgb(var(--color-primary))] mr-2"></div>Loading...';
            container.style.position = 'relative';
            container.appendChild(loader);
        }
        if (loader) {
            loader.classList.toggle('hidden', !isLoading);
        }
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

    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (!overlay) return;
        if (show) {
            const hasOpsData = charts.ops?.data?.datasets?.[0]?.data?.length > 0;
            const hasNetworkData = charts.network?.data?.datasets?.[0]?.data?.length > 0 || charts.network?.data?.datasets?.[1]?.data?.length > 0;
            const hasCommandsData = charts.commands?.data?.datasets?.[0]?.data?.some(v => (v || 0) > 0);
            const hasAnyData = hasOpsData || hasNetworkData || hasCommandsData;
            if (hasAnyData) {
                return;
            }
            overlay.classList.remove('hidden');
        } else {
            overlay.classList.add('hidden');
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
                        legend: { display: false },
                        title: { display: true, text: 'Operations Per Second' }
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
                        tooltip: { enabled: false },
                        title: { display: true, text: 'Memory Usage' }
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
                type: 'bar',
                devicePixelRatio: 1,
                data: {
                    labels: [],
                    datasets: [{
                        type: 'bar',
                        label: 'Ops / Second',
                        data: [],
                        backgroundColor: themeColor,
                        borderWidth: 0,
                        borderRadius: 4,
                        yAxisID: 'yOps'
                    },
                    {
                        type: 'line',
                        label: 'Memory (MB)',
                        data: [],
                        borderColor: accentBlue,
                        backgroundColor: accentBlueFill,
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'yMem'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        title: { display: true, text: 'Commands vs Memory' }
                    },
                    scales: {
                        x: { 
                            grid: { display: false },
                            title: { display: true, text: 'Time' }
                        },
                        yOps: { 
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: { display: true, text: 'Ops / Second' },
                            ticks: { precision: 0 }
                        },
                        yMem: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: false,
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'Memory (MB)' }
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
                        tooltip: { enabled: false },
                        title: { display: true, text: 'Cache Hit Ratio' }
                    }
                }
            });
            charts.cache = chart;
            setChartLoading('redisCacheHitRatio', true);
            setGaugeCenter('redisCacheHitRatio', 0, 'Hit Ratio');
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
                            label: 'Input',
                            data: [],
                            borderColor: themeColor,
                            backgroundColor: themeFill,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Output',
                            data: [],
                            borderColor: themeColor,
                            backgroundColor: themeFill,
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
                        legend: { display: false },
                        title: { display: true, text: 'Network Traffic (MB/s)' }
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
        }

        // Update Memory usage gauge
        if (charts.memory && data.memoryPercent) {
            charts.memory.data.datasets[0].data = [data.memoryPercent, 100 - data.memoryPercent];
            charts.memory.update();
            setChartLoading('redisMemoryUsage', false);
            setGaugeCenter('redisMemoryUsage', data.memoryPercent, 'Usage');
        }

        // Update Commands vs Memory chart
        if (charts.commands && data.opsData && data.opsData.labels && data.memoryData && data.memoryData.values) {
            const labels = (data.opsData.labels || []).slice(-10);
            const ops = (data.opsData.values || []).slice(-10);
            const mem = (data.memoryData.values || []).slice(-10);
            const len = Math.min(labels.length, ops.length, mem.length);
            charts.commands.data.labels = labels.slice(labels.length - len);
            charts.commands.data.datasets[0].data = ops.slice(ops.length - len);
            charts.commands.data.datasets[1].data = mem.slice(mem.length - len);
            charts.commands.update();
            if (len > 0) setChartLoading('redisCommandsChart', false);
        }

        // Update Cache hit ratio gauge
        if (charts.cache && data.hitRate !== undefined) {
            charts.cache.data.datasets[0].data = [data.hitRate, 100 - data.hitRate];
            charts.cache.update();
            setChartLoading('redisCacheHitRatio', false);
            setGaugeCenter('redisCacheHitRatio', data.hitRate, 'Hit Ratio');
        }

        // Update Network traffic chart
        if (charts.network && data.networkData) {
            const labels = (data.networkData.labels || []).slice(-10);
            const input = (data.networkData.input || []).slice(-10);
            const output = (data.networkData.output || []).slice(-10);
            const len = Math.min(labels.length, input.length, output.length);
            charts.network.data.labels = labels.slice(labels.length - len);
            charts.network.data.datasets[0].data = input.slice(input.length - len);
            charts.network.data.datasets[1].data = output.slice(output.length - len);
            charts.network.update();
            if (len > 0) setChartLoading('redisNetworkTraffic', false);
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
        setText('getCommands', data.commandStats?.GET || '0');
        setText('setCommands', data.commandStats?.SET || '0');
        setText('hgetCommands', data.commandStats?.HGET || '0');
        setText('hsetCommands', data.commandStats?.HSET || '0');

        // Update cache stats
        setText('keyHits', data.keyspace_hits || '0');
        setText('keyMisses', data.keyspace_misses || '0');

        setText('networkInput', ((data.network_input || 0)).toFixed(2));
        setText('networkOutput', ((data.network_output || 0)).toFixed(2));

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

    // Fetch data from Laravel backend
    async function fetchRedisData() {
        try {
            showLoading(true);
            
            if (!serverId || isNaN(serverId)) {
                throw new Error('Invalid server ID');
            }

            const response = await fetch(`/admin/server/${serverId}/redis-data`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.ok) {
                updateApiStatus(false, result.message || 'API error');
                showLoading(false);
                return;
            }

            // Update API status
            updateApiStatus(true);
            
            // Extract data from the API response
            const redisData = result.summary || {};
            const chartData = result.chartData || {};
            serverIp = result.ip || serverIp;
            
            // Prepare data for UI using the actual Redis metrics structure
            const uiData = {
                connected_clients: redisData.connected_clients || 0,
                instantaneous_ops_per_sec: redisData.instantaneous_ops_per_sec || 0,
                used_memory: (redisData.used_memory || 0) * 1024 * 1024, // Convert MB to bytes
                total_system_memory: (redisData.total_system_memory || 0) * 1024 * 1024, // Convert MB to bytes
                total_keys: redisData.db0 ? redisData.db0.match(/keys=(\d+)/)?.[1] || 0 : redisData.total_keys || 0,
                keyspace_hits: redisData.keyspace_hits || 0,
                keyspace_misses: redisData.keyspace_misses || 0,
                hit_rate: redisData.hit_rate || 0,
                connected_slaves: redisData.connected_slaves || 0,
                blocked_clients: redisData.blocked_clients || 0,
                expired_keys: redisData.expired_keys || 0,
                evicted_keys: redisData.evicted_keys || 0,
                network_input: redisData.network_input || 0,
                network_output: redisData.network_output || 0,
                uptime: redisData.uptime_in_seconds ? 
                    Math.floor(redisData.uptime_in_seconds / 86400) + ' days' : '--',
                redis_version: redisData.redis_version || '--',
                commandStats: {
                    GET: redisData.commandstats_get || 0,
                    SET: redisData.commandstats_set || 0,
                    HGET: redisData.commandstats_hget || 0,
                    HSET: redisData.commandstats_hset || 0
                }
            };

            // Calculate hit rate if not provided
            if (uiData.hit_rate === 0 && uiData.keyspace_hits > 0) {
                const total = uiData.keyspace_hits + uiData.keyspace_misses;
                uiData.hit_rate = total > 0 ? (uiData.keyspace_hits / total * 100) : 0;
            }

            // Prepare chart data
            const chartUpdateData = {
                opsData: {
                    labels: chartData.ops?.labels || [],
                    values: chartData.ops?.data || []
                },
                memoryPercent: redisData.memory_percent || 0,
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

            // Calculate memory percentage if not provided
            if (chartUpdateData.memoryPercent === 0 && uiData.used_memory && uiData.total_system_memory) {
                chartUpdateData.memoryPercent = (uiData.used_memory / uiData.total_system_memory) * 100;
            }

            // Update UI and charts
            updateUI(uiData);
            updateCharts(chartUpdateData);
            updateLastUpdated();

        } catch (error) {
            console.error('Failed to fetch Redis data:', error);
            updateApiStatus(false, 'Connection failed');
            
            // Show error in UI
            document.querySelectorAll('.text-xl.font-semibold, .text-lg.font-semibold').forEach(el => {
                if (el.textContent === '--' || el.textContent === '0') {
                    el.textContent = 'Error';
                }
            });
            
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
