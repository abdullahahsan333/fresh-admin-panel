@extends('layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Linux</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: <span id="last-updated-time">{{ now()->format('M d, H:i') }}</span></div>
    </div>
</header>

<!-- Top Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    <!-- Server Status -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col justify-between">
        <div>
            <h2 class="text-gray-700 font-medium mb-4">Server: {{ $server->ip }}</h2>
            <div class="flex items-center gap-2 mb-4">
                <span class="text-gray-500">Status:</span>
                @php
                    $linuxData = fetchLinuxData($server->ip);
                    $serverStatus = getServerStatus($linuxData);
                    $statusColors = [
                        'healthy' => ['text' => 'text-green-600', 'bg' => 'bg-green-500'],
                        'warning' => ['text' => 'text-yellow-600', 'bg' => 'bg-yellow-500'],
                        'critical' => ['text' => 'text-red-600', 'bg' => 'bg-red-500'],
                        'unknown' => ['text' => 'text-gray-600', 'bg' => 'bg-gray-500']
                    ];
                    $statusTexts = [
                        'healthy' => 'Running',
                        'warning' => 'Warning',
                        'critical' => 'Critical',
                        'unknown' => 'Unknown'
                    ];
                @endphp
                <span class="{{ $statusColors[$serverStatus]['text'] }} font-medium">{{ $statusTexts[$serverStatus] }}</span>
                <span class="h-3 w-3 rounded-full {{ $statusColors[$serverStatus]['bg'] }}"></span>
            </div>
            
            @php
                $linuxSummary = calculateLinuxSummary($linuxData);
            @endphp
            
            <div class="mb-2">
                <span class="text-[rgb(var(--color-primary))] text-3xl font-semibold">CPU: {{ $linuxSummary['cpu_usage'] ?? '--' }}%</span>
            </div>
            <div class="text-gray-500 mb-6">
                Memory: {{ $linuxSummary['memory_used'] ?? '--' }} MB used ({{ $linuxSummary['memory_percent'] ?? '--' }}%)
            </div>
        </div>
        <div>
            <button class="bg-[rgb(var(--color-primary))] hover:bg-[rgb(var(--color-primary)/.9)] text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                View Details
            </button>
        </div>
    </div>

    <!-- Server Metrics -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h2 class="text-gray-700 font-medium mb-2">Server Metrics</h2>
        <div class="text-xs text-gray-400 mb-6">Last Updated: <span id="metrics-updated">{{ now()->format('H:i:s') }}</span></div>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- CPU -->
            <div class="flex flex-col gap-1">
                <div class="h-10 w-10 rounded bg-[rgb(var(--color-primary)/.12)] flex items-center justify-center text-[rgb(var(--color-primary))] mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <span class="text-xs text-gray-500">CPU</span>
                <span class="text-sm font-medium">{{ $linuxSummary['cpu_usage'] ?? '--' }} %</span>
            </div>

            <!-- Memory -->
            <div class="flex flex-col gap-1">
                <div class="h-10 w-10 rounded bg-green-100 flex items-center justify-center text-green-600 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs text-gray-500">Memory Used</span>
                <span class="text-sm font-medium">{{ $linuxSummary['memory_used'] ?? '--' }} MB</span>
            </div>

            <!-- Disk -->
            <div class="flex flex-col gap-1">
                <div class="h-10 w-10 rounded bg-yellow-100 flex items-center justify-center text-yellow-600 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <span class="text-xs text-gray-500">Disk Used</span>
                <span class="text-sm font-medium">{{ $linuxSummary['disk_percent'] ?? '--' }} %</span>
            </div>

            <!-- Load Avg -->
            <div class="flex flex-col gap-1">
                <div class="h-10 w-10 rounded bg-blue-100 flex items-center justify-center text-blue-500 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
                <span class="text-xs text-gray-500">Load Avg</span>
                <span class="text-sm font-medium">{{ $linuxSummary['load_avg_1'] ?? '--' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Usage Cards -->
@php
    $chartData = [
        'cpu' => processLatestChartData($linuxData, 'cpu_usage_percent', 6),
        'memory' => processLatestChartData($linuxData, 'memory_used_percent', 6),
        'disk' => processLatestChartData($linuxData, 'disk_usage_percent', 6)
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- CPU Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="py-4">
            <div id="cpu-usage-chart" class="h-36"></div>
            <span class="text-2xl font-semibold text-gray-800">{{ $linuxSummary['cpu_usage'] ?? '--' }}%</span>
            <span class="text-sm text-gray-500 ml-2">Latest CPU Usage</span>
        </div>
    </div>

    <!-- RAM Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="py-4">
            <div id="ram-usage-chart" class="h-36"></div>
            <span class="text-2xl font-semibold text-gray-800">{{ $linuxSummary['memory_percent'] ?? '--' }}%</span>
            <span class="text-sm text-gray-500 ml-2">Latest RAM Usage</span>
        </div>
    </div>

    <!-- Disk Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="py-4">
            <div id="disk-usage-chart" class="h-36"></div>
            <span class="text-2xl font-semibold text-gray-800">{{ $linuxSummary['disk_percent'] ?? '--' }}%</span>
            <span class="text-sm text-gray-500 ml-2">Latest Disk Usage</span>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Top Resource Consumers -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 lg:col-span-1">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Top Resource Consumers</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-2">NAME</th>
                        <th scope="col" class="px-3 py-2">CPU</th>
                        <th scope="col" class="px-3 py-2">MEM</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $topProcesses = getTopProcesses($linuxData, 6);
                    @endphp
                    
                    @if(empty($topProcesses))
                        <!-- Placeholder row -->
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">-</td>
                            <td class="px-3 py-2">-</td>
                            <td class="px-3 py-2">-</td>
                        </tr>
                    @else
                        @foreach($topProcesses as $process)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">{{ $process['name'] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $process['cpu'] ?? '-' }}%</td>
                                <td class="px-3 py-2">{{ $process['mem'] ?? '-' }} MB</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Logs -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 lg:col-span-1">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">System Logs</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div class="h-full overflow-y-auto text-sm">
            @if(!empty($linuxData) && count($linuxData) > 0)
                <div class="space-y-2">
                    @foreach(array_slice($linuxData, -3) as $log)
                        <div class="text-gray-600">
                            <span class="text-gray-400 text-xs">{{ $log['timestamp'] ?? 'N/A' }}</span>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                <span>CPU: {{ $log['metrics']['cpu_usage_percent'] ?? 0 }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-full flex items-center justify-center text-gray-400">
                    No log data available
                </div>
            @endif
        </div>
    </div>

    <!-- Traffic -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Input Traffic -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <div id="inputTrafficChart" class="h-24"></div>
        </div>

        <!-- Output Traffic -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <div id="outputTrafficChart" class="h-24"></div>
        </div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartData = @json($chartData);
    const serverIp = @json($server->ip);
    const dataUrl = "{{ route('admin.server.linux.data', $server->id) }}";
    
    function createChartOptions(title, labels, data, color) {
        return {
            series: [{
                name: title,
                data: data
            }],
            chart: {
                height: 140,
                type: 'area',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            title: {
                text: title,
                align: 'left',
                style: { fontSize: '12px', fontWeight: 600, color: '#374151' }
            },
            colors: [color],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 3,
                yaxis: { lines: { show: true } }
            },
            xaxis: {
                categories: labels,
                labels: {
                    show: true,
                    style: { colors: '#6b7280', fontSize: '10px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
                title: { text: 'Time', style: { color: '#6b7280', fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    show: true,
                    style: { colors: '#6b7280', fontSize: '10px' },
                    formatter: function(val) { return val.toFixed(1) + '%'; }
                },
                min: 0,
                title: { text: 'Percent', style: { color: '#6b7280', fontSize: '11px' } }
            },
            tooltip: {
                x: { format: 'HH:mm' },
                y: { formatter: function(val) { return val.toFixed(1) + '%' } }
            }
        };
    }
    
    function createTrafficChartOptions(title, labels, inData, outData) {
        return {
            series: [
                { name: 'Input', data: inData },
                { name: 'Output', data: outData }
            ],
            chart: {
                height: 100,
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            title: {
                text: title,
                align: 'left',
                style: { fontSize: '12px', fontWeight: 600, color: '#374151' }
            },
            colors: ['#3b82f6', '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 3,
                yaxis: { lines: { show: true } }
            },
            xaxis: {
                categories: labels,
                labels: {
                    show: true,
                    style: { colors: '#6b7280', fontSize: '10px' }
                },
                title: { text: 'Time', style: { color: '#6b7280', fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    show: true,
                    style: { colors: '#6b7280', fontSize: '10px' },
                    formatter: function(val) { return val.toFixed(1) + ' MB'; }
                },
                title: { text: 'MB', style: { color: '#6b7280', fontSize: '11px' } }
            },
            legend: { show: false },
            tooltip: {
                x: { format: 'HH:mm' },
                y: { formatter: function(val) { return val.toFixed(2) + ' MB'; } }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cpuOptions = createChartOptions(
            'CPU Usage',
            chartData.cpu.labels || [],
            chartData.cpu.data || [],
            '#3b82f6'
        );
        const cpuChart = new ApexCharts(document.querySelector("#cpu-usage-chart"), cpuOptions);
        cpuChart.render();

        const ramOptions = createChartOptions(
            'RAM Usage',
            chartData.memory.labels || [],
            chartData.memory.data || [],
            '#8b5cf6'
        );
        const ramChart = new ApexCharts(document.querySelector("#ram-usage-chart"), ramOptions);
        ramChart.render();

        const diskOptions = createChartOptions(
            'Disk Usage',
            chartData.disk.labels || [],
            chartData.disk.data || [],
            '#f59e0b'
        );
        const diskChart = new ApexCharts(document.querySelector("#disk-usage-chart"), diskOptions);
        diskChart.render();

        @php
            $networkData = processLatestChartData($linuxData, 'net_input_mb', 6);
            $networkDataOut = processLatestChartData($linuxData, 'net_output_mb', 6);
        @endphp
        
        const trafficOptions = createTrafficChartOptions(
            'Network Traffic',
            @json($networkData['labels'] ?? []),
            @json($networkData['data'] ?? []),
            @json($networkDataOut['data'] ?? [])
        );
        
        const inputTrafficOptions = {
            ...trafficOptions,
            series: [{ name: 'Input', data: @json($networkData['data'] ?? []) }],
            colors: ['#3b82f6']
        };
        const inputTrafficChart = new ApexCharts(document.querySelector("#inputTrafficChart"), inputTrafficOptions);
        inputTrafficChart.render();

        const outputTrafficOptions = {
            ...trafficOptions,
            series: [{ name: 'Output', data: @json($networkDataOut['data'] ?? []) }],
            colors: ['#ef4444']
        };
        const outputTrafficChart = new ApexCharts(document.querySelector("#outputTrafficChart"), outputTrafficOptions);
        outputTrafficChart.render();

        let isUpdating = false;
        
        async function updateDashboard() {
            if (isUpdating) return;
            isUpdating = true;
            
            try {
                const response = await fetch(dataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                if (!contentType.includes('application/json')) throw new Error('Invalid content-type');
                const data = await response.json();
                
                if (data.ok) {
                    cpuChart.updateSeries([{ data: data.chartData.cpu.data }]);
                    ramChart.updateSeries([{ data: data.chartData.memory.data }]);
                    diskChart.updateSeries([{ data: data.chartData.disk.data }]);
                    
                    document.querySelector('[data-cpu]').textContent = data.summary.cpu_usage + '%';
                    document.querySelector('[data-memory]').textContent = data.summary.memory_percent + '%';
                    document.querySelector('[data-disk]').textContent = data.summary.disk_percent + '%';
                    
                    const now = new Date();
                    document.getElementById('last-updated-time').textContent = 
                        now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' +
                        now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    document.getElementById('metrics-updated').textContent = 
                        now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }
            } catch (error) {
                console.error('Update failed:', error);
            } finally {
                isUpdating = false;
            }
        }
        setInterval(updateDashboard, 30000);
    });
</script>
@endpush
