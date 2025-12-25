@extends('layouts.user')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">MongoDB</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: {{ now()->format('M d, H:i') }}</div>
    </div>
</header>

<!-- Top Row: 4 Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Current Connections -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-[rgb(var(--color-primary)/.12)] flex items-center justify-center text-[rgb(var(--color-primary))]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Current Connections</div>
        <div class="text-xs text-gray-500">Active connections</div>
    </div>

    <!-- Queries / Sec -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-yellow-100 flex items-center justify-center text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Queries / Sec</div>
        <div class="text-xs text-gray-500">Query operations per second</div>
    </div>

    <!-- Memory Usage -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-red-100 flex items-center justify-center text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Memory Usage</div>
        <div class="text-xs text-gray-500">Resident memory (MB)</div>
    </div>

    <!-- Available Connections -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Available Connections</div>
        <div class="text-xs text-gray-500">Available connections</div>
    </div>
</div>

<!-- Second Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Operations Per Second -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div id="mongoOpsPerSecond" class="h-32"></div>
    </div>

    <!-- MongoDB Connection Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">MongoDB Connection Usage</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="flex-1 flex flex-col items-center justify-center">
            <div id="mongoConnectionUsage" class="rounded-full flex items-center justify-center text-[rgb(var(--color-primary))] text-lg font-medium"></div>
            <div class="w-full flex justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded bg-[rgb(var(--color-primary)/.2)]"></span>
                    <div>
                        <div class="font-medium text-gray-700">Current Connections</div>
                        <div class="text-gray-500">0</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded bg-blue-200"></span>
                    <div>
                        <div class="font-medium text-gray-700">Available Connections</div>
                        <div class="text-gray-500">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memory Usage Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div id="mongoMemoryUsage" class="h-32"></div>
    </div>
</div>

<!-- Third Row -->
<div class="grid grid-cols-2 gap-6 mb-6">
    <!-- MongoDB Operations Overview -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">MongoDB Operations Overview</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Query Operations
                </span>
                <span class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Insert Operations
                </span>
                <span class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Update Operations
                </span>
                <span class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                <span class="text-gray-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Delete Operations
                </span>
                <span class="font-medium text-gray-800">0</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 flex items-center gap-2">
                    <span class="w-4 h-4 bg-gray-800 rounded-sm"></span>
                    Command Operations
                </span>
                <span class="font-medium text-gray-800">0</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-gray-700 font-medium">Concerns</h3>
                <div class="text-xs text-gray-500 mt-1">Errors: 0, Warnings: 0</div>
            </div>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="border-b border-gray-200 mb-6">
            <nav id="mongoConcernsTabs" class="-mb-px flex space-x-8">
                <button data-tab="slow" class="border-purple-500 text-purple-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">Slow Queries</button>
                <button data-tab="warns" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">Warns</button>
                <button data-tab="errors" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">Errors</button>
            </nav>
        </div>
        
        <div id="mongoConcernsContent" class="w-full min-h-[8rem]"></div>
    </div>
</div>

<!-- Fourth Row: Split -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Bytes Sent/Received -->
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
        <div id="mongoNetworkTraffic" class="h-32"></div>
    </div>
    
    <!-- Center Column: Operations vs Memory -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Operations vs Memory</h3>
        </div>
        <div id="mongoOpsVsMemory" class="h-32"></div>
    </div>
    
    <!-- Right Column: Disk I/O -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Disk I/O</h3>
        </div>
        <div id="mongoDiskIO" class="h-32"></div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const cp = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim();
    const cpCsv = cp.replace(/\s+/g, ',');
    const themeColor = `rgb(${cpCsv})`;
    const sparkBase = {
        chart: { type: 'line', height: 128, sparkline: { enabled: true } },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        colors: [themeColor],
        series: [{ data: [0, 0, 0, 0, 0, 0] }]
    };
    new ApexCharts(document.querySelector('#mongoOpsPerSecond'), sparkBase).render();
    new ApexCharts(document.querySelector('#mongoMemoryUsage'), { ...sparkBase, chart: { type: 'area', height: 128, sparkline: { enabled: true } } }).render();
    new ApexCharts(document.querySelector('#mongoNetworkTraffic'), sparkBase).render();
    new ApexCharts(document.querySelector('#mongoOpsVsMemory'), { 
        ...sparkBase, 
        chart: { type: 'area', height: 128, sparkline: { enabled: true } }, 
        series: [
            { name: 'Ops', data: [0, 0, 0, 0, 0, 0] },
            { name: 'Memory', data: [0, 0, 0, 0, 0, 0] }
        ] 
    }).render();
    new ApexCharts(document.querySelector('#mongoDiskIO'), sparkBase).render();
    var mongoConnectionUsageOptions = {
        series: [67],
        chart: { height: 150, type: 'radialBar', offsetY: -10 },
        colors: [themeColor],
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: { fontSize: '16px', color: undefined, offsetY: 120 },
                    value: { offsetY: 76, fontSize: '22px', color: undefined, formatter: function (val) { return val + "%"; } }
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: { shade: 'dark', shadeIntensity: 0.15, inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 50, 65, 91] }
        },
        stroke: { dashArray: 4 },
        labels: ['Connection Usage']
    };
    new ApexCharts(document.querySelector('#mongoConnectionUsage'), mongoConnectionUsageOptions).render();
    (function(){
        var activeTab = 'slow';
        var tabsEl = document.getElementById('mongoConcernsTabs');
        var contentEl = document.getElementById('mongoConcernsContent');
        function renderContent() {
            if (activeTab === 'slow') {
                contentEl.innerHTML = '<div class="text-sm text-gray-500">No slow queries</div>';
            } else if (activeTab === 'warns') {
                contentEl.innerHTML = '<div class="text-sm text-gray-500">No warnings</div>';
            } else if (activeTab === 'errors') {
                contentEl.innerHTML = '<div class="text-sm text-gray-500">No errors</div>';
            }
        }
        function setActiveTab(tab) {
            activeTab = tab;
            tabsEl.querySelectorAll('button').forEach(function(btn){
                if (btn.dataset.tab === tab) {
                    btn.className = 'border-purple-500 text-purple-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm';
                } else {
                    btn.className = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm';
                }
            });
            renderContent();
        }
        tabsEl.addEventListener('click', function(e){
            var btn = e.target.closest('button[data-tab]');
            if (!btn) return;
            e.preventDefault();
            setActiveTab(btn.dataset.tab);
        });
        setActiveTab('slow');
    })();
</script>
@endpush
