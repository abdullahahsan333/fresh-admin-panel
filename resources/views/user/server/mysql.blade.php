@extends('layouts.user')

@section('content')


<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">MySQL</h1>
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
        <div class="text-xs text-gray-500">Live DB connections</div>
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
        <div class="text-xs text-gray-500">Avg query throughput</div>
    </div>

    <!-- Slow Queries -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-red-100 flex items-center justify-center text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Slow Queries</div>
        <div class="text-xs text-gray-500">Cumulative slow queries</div>
    </div>

    <!-- Threads Running -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Threads Running</div>
        <div class="text-xs text-gray-500">Active DB worker threads</div>
    </div>
</div>

<!-- Second Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Queries Per Second -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Queries Per Second</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div class="py-4">
            <span class="text-2xl font-semibold text-gray-800">--</span>
            <span class="text-sm text-gray-500 ml-2">Latest QPS</span>
        </div>
    </div>

    <!-- MySQL Connection Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
        <div class="w-full flex justify-between items-start">
            <h3 class="text-gray-700 font-medium">MySQL Connection Usage</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="mt-2 relative flex items-center justify-center">
            <div id="connectionUsage" class="w-full h-36"></div>
        </div>

        <div class="flex gap-8 text-left w-full justify-center">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-[rgb(var(--color-primary)/.2)] rounded-sm"></div>
                    <span class="text-xs text-gray-500">Current Connections</span>
                </div>
                <div class="text-lg font-semibold pl-5">250</div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-200 rounded-sm"></div>
                    <span class="text-xs text-gray-500">Max Connections</span>
                </div>
                <div class="text-lg font-semibold pl-5">400</div>
            </div>
        </div>
    </div>

    <!-- Load vs Query Time -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Load vs Query Time</h3>
        </div>
        <!-- Chart placeholder -->
        <div class="h-48"></div>
    </div>
</div>

<!-- Third Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- MySQL Query Overview -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">MySQL Query Overview</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="mb-4 flex h-8 w-full rounded overflow-hidden">
            <div class="bg-[rgb(var(--color-primary))] text-white text-xs flex items-center justify-center font-bold" style="width: 70%">SELECT 100.0%</div>
            <div class="bg-green-500 text-white text-xs flex items-center justify-center font-bold" style="width: 10%">INS</div>
            <div class="bg-blue-400 text-white text-xs flex items-center justify-center font-bold" style="width: 10%">UP</div>
            <div class="bg-yellow-400 text-white text-xs flex items-center justify-center font-bold" style="width: 10%">DEL</div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                    <span class="text-gray-600">SELECT Avg Query Time</span>
                </div>
                <span class="font-medium">1318.21 ms</span>
            </div>
            <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="text-gray-400 text-lg leading-none">+</span>
                    <span class="text-gray-600">INSERT Avg Query Time</span>
                </div>
                <span class="font-medium">0.00 ms</span>
            </div>
            <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-2">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <span class="text-gray-600">UPDATE Avg Query Time</span>
                </div>
                <span class="font-medium">0.00 ms</span>
            </div>
            <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-2">
                <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span class="text-gray-600">DELETE Avg Query Time</span>
                </div>
                <span class="font-medium">0.00 ms</span>
            </div>
        </div>
    </div>

    <!-- Table Open Cache -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative flex flex-col items-center justify-center text-center">
        <div class="w-full flex justify-between items-start absolute top-6 px-6">
            <h3 class="text-gray-700 font-medium">Table Open Cache</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="mt-6">
            <div id="tableOpenCache" class="w-full h-36"></div>
        </div>

        <div class="flex gap-6 text-left w-full justify-center mt-auto">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-200 rounded-sm"></div>
                    <span class="text-xs text-gray-500">Open Tables</span>
                </div>
                <div class="text-lg font-semibold pl-5">0</div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-200 rounded-sm"></div>
                    <span class="text-xs text-gray-500">Table Open Cache</span>
                </div>
                <div class="text-lg font-semibold pl-5">0</div>
            </div>
        </div>
    </div>

    <!-- InnoDB Buffer Pool -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative flex flex-col items-center justify-center text-center">
        <div class="w-full flex justify-between items-start absolute top-6 px-6">
            <h3 class="text-gray-700 font-medium">InnoDB Buffer Pool</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>

        <div class="mt-6">
            <div id="innodbBufferPool" class="w-full h-36"></div>
        </div>

        <div class="flex gap-6 text-left w-full justify-center mt-auto">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-200 rounded-sm"></div>
                    <span class="text-xs text-gray-500">Total Size</span>
                </div>
                <div class="text-lg font-semibold pl-5">---</div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-200 rounded-sm"></div>
                    <span class="text-xs text-gray-500">Reads From Disk</span>
                </div>
                <div class="text-lg font-semibold pl-5">820</div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Concerns -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 lg:col-span-2">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-gray-700 font-medium">Concerns</h3>
                <p class="text-xs text-gray-400">Count Of Errors, Warns and Slow Queries</p>
            </div>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <div class="border-b border-gray-200 mb-4">
            <nav id="mysqlConcernsTabs" class="flex space-x-6">
                <button data-tab="slow" class="px-4 py-2 text-purple-600 border-b-2 border-purple-600 font-medium text-sm">Slow Queries</button>
                <button data-tab="warns" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium text-sm">Warns</button>
                <button data-tab="errors" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium text-sm">Errors</button>
            </nav>
        </div>

        <div id="mysqlConcernsContent" class="w-full min-h-[8rem]"></div>
    </div>

    <!-- Traffic -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Bytes Sent -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <div id="bytesSent" class="w-full h-24"></div>
        </div>

        <!-- Bytes Received -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <div id="bytesReceived" class="w-full h-24"></div>
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
    var options = {
            series: [67],
            chart: {
            height: 150,
            type: 'radialBar',
            offsetY: -10
        },
        colors: [themeColor],
        plotOptions: {
            radialBar: {
            startAngle: -135,
            endAngle: 135,
            dataLabels: {
                name: {
                    fontSize: '16px',
                    color: undefined,
                    offsetY: 120
                },
                value: {
                    offsetY: 76,
                    fontSize: '22px',
                    color: undefined,
                    formatter: function (val) {
                        return val + "%";
                    }
                }
            }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                shadeIntensity: 0.15,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 65, 91]
            },
        },
        stroke: {
            dashArray: 4
        },
        labels: ['Connection Usage'],
    };

    var TableOpenCacheOptions = {
        series: [67],
        chart: {
            height: 150,
            type: 'radialBar',
            offsetY: -10
        },
        colors: [themeColor],
        plotOptions: {
            radialBar: {
            startAngle: -135,
            endAngle: 135,
            dataLabels: {
                name: {
                    fontSize: '16px',
                    color: undefined,
                    offsetY: 120
                },
                value: {
                    offsetY: 76,
                    fontSize: '22px',
                    color: undefined,
                    formatter: function (val) {
                        return val + "%";
                    }
                }
            }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                shadeIntensity: 0.15,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 65, 91]
            },
        },
        stroke: {
            dashArray: 4
        },
        labels: ['Cache Usage'],
    };
    var InnodbBufferPoolOptions = {
        series: [67],
        chart: {
            height: 150,
            type: 'radialBar',
            offsetY: -10
        }
    };
    var connectionUsage = new ApexCharts(document.querySelector("#connectionUsage"), options);
    connectionUsage.render();
    var tableOpenCache = new ApexCharts(document.querySelector("#tableOpenCache"), TableOpenCacheOptions);
    tableOpenCache.render();
    var innodbBufferPool = new ApexCharts(document.querySelector("#innodbBufferPool"), InnodbBufferPoolOptions);
    innodbBufferPool.render();
    var bytesSent = new ApexCharts(document.querySelector("#bytesSent"), {
        series: [{ name: "Sent", data: [10, 20, 30, 25, 35, 40] }],
        chart: { height: 100, type: 'line' },
        stroke: { curve: 'smooth' },
        dataLabels: { enabled: false }
    });
    bytesSent.render();
    var bytesReceived = new ApexCharts(document.querySelector("#bytesReceived"), {
        series: [{ name: "Received", data: [5, 15, 25, 20, 30, 35] }],
        chart: { height: 100, type: 'line' },
        stroke: { curve: 'smooth' },
        dataLabels: { enabled: false }
    });
    bytesReceived.render();
    (function(){
        var activeTab = 'slow';
        var tabsEl = document.getElementById('mysqlConcernsTabs');
        var contentEl = document.getElementById('mysqlConcernsContent');
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
                    btn.className = 'px-4 py-2 text-purple-600 border-b-2 border-purple-600 font-medium text-sm';
                } else {
                    btn.className = 'px-4 py-2 text-gray-500 hover:text-gray-700 font-medium text-sm';
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
