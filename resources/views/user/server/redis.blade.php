@extends('layouts.user')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Redis</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: {{ now()->format('M d, H:i') }}</div>
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
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Connected Clients</div>
        <div class="text-xs text-gray-500">Active Redis connections</div>
    </div>

    <!-- Ops / Sec -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-yellow-100 flex items(center) justify-center text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
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
            <span class="text-xl font-semibold text-gray-800">--</span>
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
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Total Keys</div>
        <div class="text-xs text-gray-500">Keys in database</div>
    </div>
</div>

<!-- Second Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Operations Per Second -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div id="redisOpsPerSecond" class="h-24 w-full"></div>
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
                        <div class="text-gray-500">0B</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-blue-200"></span>
                    <div>
                        <div class="font-medium text-gray-700">Total System</div>
                        <div class="text-gray-500">0B</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commands vs Memory -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div id="redisCommandsMemory" class="h-32"></div>
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
                <span class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    SET Commands
                </span>
                <span class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    HGET Commands
                </span>
                <span class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    HSET Commands
                </span>
                <span class="font-medium text-gray-800">0</span>
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
            <div id="redisCacheHitRatio" class="h-36 w-full"></div>
            <div class="w-full flex justify-between px-4 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-green-200"></span>
                    <div>
                        <div class="font-medium text-gray-700">Key Hits</div>
                        <div class="text-gray-500">0</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-red-200"></span>
                    <div>
                        <div class="font-medium text-gray-700">Key Misses</div>
                        <div class="text-gray-500">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Traffic -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col">
        <div class="flex-1 flex items-center justify-center text-gray-400 text-sm mb-4">
            <div id="redisNetworkTraffic" class="h-36 w-full"></div>
        </div>

        <div class="flex justify-between items-end mt-auto">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-8 h-8 rounded bg-purple-100"></span>
                    <div>
                        <div class="text-xs text-gray-500">Input Kb/s</div>
                        <div class="text-lg font-medium text-gray-800">0</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-8 h-8 rounded bg-orange-100"></span>
                    <div>
                        <div class="text-xs text-gray-500">Output Kb/s</div>
                        <div class="text-lg font-medium text-gray-800">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const cp = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim();
    const cpCsv = cp.replace(/\s+/g, ',');
    const themeColor = `rgb(${cpCsv})`;
    const sparkOpts = {
        chart: { type: 'line', height: 80, sparkline: { enabled: true } },
        stroke: { curve: 'smooth', width: 2 },
        series: [{ data: [0, 0, 0, 0, 0, 0] }],
        colors: [themeColor],
        dataLabels: { enabled: false }
    };
    new ApexCharts(document.querySelector("#redisOpsPerSecond"), sparkOpts).render();
    new ApexCharts(document.querySelector("#redisMemoryUsage"), { ...sparkOpts, chart: { type: 'area', height: 144, sparkline: { enabled: true } } }).render();
    new ApexCharts(document.querySelector("#redisCommandsMemory"), sparkOpts).render();
    new ApexCharts(document.querySelector("#redisCacheHitRatio"), { ...sparkOpts, chart: { type: 'area', height: 144, sparkline: { enabled: true } }, series: [{ data: [0, 0, 0, 0, 0] }] }).render();
    new ApexCharts(document.querySelector("#redisNetworkTraffic"), { ...sparkOpts, series: [{ data: [0, 0, 0, 0, 0, 0] }] }).render();
</script>
@endpush
