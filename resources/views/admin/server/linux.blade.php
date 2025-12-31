@extends('layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Linux</h1>
        <div id="apiStatusDot" class="hidden">
            <span class="inline-block w-2 h-2 rounded-full"></span>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <button onclick="refreshLinuxData()" class="p-2 text-gray-500 hover:text-blue-600 transition-colors rounded-full hover:bg-blue-50 focus:outline-none" title="Refresh Data">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </button>
        <div class="text-sm text-gray-500">Last updated: <span id="last-updated-time">{{ now()->format('M d, H:i') }}</span></div>
    </div>
</header>

<!-- Toast container injected dynamically -->
<div id="toast-container" class="fixed top-4 right-4 z-50"></div>

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
                <span id="serverStatusText" class="{{ $statusColors[$serverStatus]['text'] }} font-medium">{{ $statusTexts[$serverStatus] }}</span>
                <span id="serverStatusDot" class="h-3 w-3 rounded-full {{ $statusColors[$serverStatus]['bg'] }}"></span>
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
                <span id="loadAvgText" class="text-sm font-medium">{{ ($linuxSummary['load_avg_1'] ?? '--') }}, {{ ($linuxSummary['load_avg_5'] ?? '--') }}, {{ ($linuxSummary['load_avg_15'] ?? '--') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Usage Cards -->
@php
    $diskLabels = [];
    $diskSeries = [];
    if (!empty($linuxData)) {
        $slice = array_slice($linuxData, -6);
        foreach ($slice as $point) {
            $timestamp = $point['timestamp'] ?? '';
            $metrics = $point['metrics'] ?? [];
            $used = (float)($metrics['disk_used_gb'] ?? 0);
            $total = (float)($metrics['disk_total_gb'] ?? 0);
            $pct = $total > 0 ? round(($used / $total) * 100, 2) : 0.0;
            if ($timestamp) {
                try {
                    $date = new \DateTime($timestamp);
                    $diskLabels[] = $date->format('H:i');
                } catch (\Exception $e) {
                    $diskLabels[] = $timestamp;
                }
            } else {
                $diskLabels[] = '';
            }
            $diskSeries[] = $pct;
        }
    }
    $chartData = [
        'cpu' => processLatestChartData($linuxData, 'cpu_usage_percent', 6),
        'memory' => processLatestChartData($linuxData, 'memory_used_percent', 6),
        'disk' => [
            'labels' => $diskLabels,
            'data' => $diskSeries,
        ],
        'network' => [
            'labels' => processLatestChartData($linuxData, 'net_input_mb', 6)['labels'] ?? [],
            'input' => processLatestChartData($linuxData, 'net_input_mb', 6)['data'] ?? [],
            'output' => processLatestChartData($linuxData, 'net_output_mb', 6)['data'] ?? []
        ]
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- CPU Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="py-4">
            <h3 class="text-gray-700 font-medium mb-2">CPU Usage</h3>
            <div id="cpu-usage-chart" class="h-52"></div>
            <span class="text-2xl font-semibold text-gray-800">{{ $linuxSummary['cpu_usage'] ?? '--' }}%</span>
            <span class="text-sm text-gray-500 ml-2">Latest CPU Usage</span>
        </div>
    </div>

    <!-- RAM Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="py-4">
            <h3 class="text-gray-700 font-medium mb-2">RAM Usage</h3>
            <div id="ram-usage-chart" class="h-52"></div>
            <span class="text-2xl font-semibold text-gray-800">{{ $linuxSummary['memory_percent'] ?? '--' }}%</span>
            <span class="text-sm text-gray-500 ml-2">Latest RAM Usage</span>
        </div>
    </div>

    <!-- Disk Usage -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
        <div class="py-4">
            <h3 class="text-gray-700 font-medium mb-2">Disk Usage</h3>
            <div id="disk-usage-chart" class="h-52"></div>
            <span class="text-2xl font-semibold text-gray-800">{{ $linuxSummary['disk_percent'] ?? '--' }}%</span>
            <span class="text-sm text-gray-500 ml-2">Latest Disk Usage</span>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Top Resource Consumers -->
    <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100 lg:col-span-1">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-gray-700 font-medium">Top Resource Consumers</h3>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div class="max-h-96 overflow-x-auto">
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
                        $topProcesses = getTopProcesses($linuxData);
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
                                <td class="whitespace-nowrap px-3 py-2 font-medium text-gray-900">{{ $process['name'] ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-2">
                                    @php $cpuVal = (float)($process['cpu'] ?? 0); @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $cpuVal < 50 ? 'bg-green-100 text-green-800' : ($cpuVal < 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ isset($process['cpu']) ? ($cpuVal . '%') : '-' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-2">
                                    @php $memVal = (float)($process['mem'] ?? 0); @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $memVal < 50 ? 'bg-green-100 text-green-800' : ($memVal < 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ isset($process['mem']) ? ($memVal . '%') : '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Logs section --}}
    <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
        <div class="px-2 pt-2">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">System logs</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="max-h-96 overflow-x-auto">
            <pre class="text-xs text-gray-600"></pre>
        </div>
    </div>

    <!-- Traffic -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Input Traffic -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <div class="px-2 pt-2">
                <div class="text-xs text-gray-600 mb-1">Network Input (MB)</div>
            </div>
            <div id="inputTrafficChart" class="h-44"></div>
        </div>

        <!-- Output Traffic -->
        <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <div class="px-2 pt-2">
                <div class="text-xs text-gray-600 mb-1">Network Output (MB)</div>
            </div>
            <div id="outputTrafficChart" class="h-44"></div>
        </div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = @json($chartData);
    const serverIp = @json($server->ip);
    const dataUrl = "{{ route('admin.server.linux.data', $server->id) }}";
    let charts = {};
    let netLabels = [];
    let netInput = [];
    let netOutput = [];
    function q(sel) { try { return document.querySelector(sel); } catch(e) { return null; } }
    function setText(target, value) {
        const el = (target.startsWith('#') || target.startsWith('.')) ? q(target) : document.getElementById(target);
        if (el) el.textContent = value;
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
    function showApiStatus(type, message) {
        const dotWrap = document.getElementById('apiStatusDot');
        if (!dotWrap) return;
        const dot = dotWrap.querySelector('span') || dotWrap;
        dotWrap.classList.remove('hidden');
        dot.className = `inline-block w-2 h-2 rounded-full ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        showToast(type, type === 'success' ? 'Connected' : (message || 'Connection failed'));
    }
    function makeLineChart(ctx, labels, data, color, titleText) {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels || [],
                datasets: [{
                    data: data || [],
                    borderColor: color,
                    backgroundColor: color.replace('rgb(', 'rgba(').replace(')', ', 0.15)'),
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
                    x: { grid: { display: false }, title: { display: true, text: 'Time' } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                }
            }
        });
    }
    function pushNetSample(inMB, outMB) {
        const label = new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
        netLabels.push(label);
        netInput.push(Number(inMB || 0));
        netOutput.push(Number(outMB || 0));
        if (netLabels.length > 12) netLabels = netLabels.slice(-12);
        if (netInput.length > 12) netInput = netInput.slice(-12);
        if (netOutput.length > 12) netOutput = netOutput.slice(-12);
    }
    document.addEventListener('DOMContentLoaded', function() {
        const cpuCtx = createChartCanvas('cpu-usage-chart');
        if (cpuCtx) charts.cpu = makeLineChart(cpuCtx, chartData.cpu.labels || [], chartData.cpu.data || [], 'rgb(59, 130, 246)', 'CPU Usage');
        const ramCtx = createChartCanvas('ram-usage-chart');
        if (ramCtx) charts.ram = makeLineChart(ramCtx, chartData.memory.labels || [], chartData.memory.data || [], 'rgb(139, 92, 246)', 'RAM Usage');
        const diskCtx = createChartCanvas('disk-usage-chart');
        if (diskCtx) charts.disk = makeLineChart(diskCtx, chartData.disk.labels || [], chartData.disk.data || [], 'rgb(245, 158, 11)', 'Disk Usage');
        const inputCtx = createChartCanvas('inputTrafficChart');
        if (inputCtx) charts.netIn = makeLineChart(inputCtx, chartData.network.labels || [], chartData.network.input || [], 'rgb(59, 130, 246)', 'Network Input (MB)');
        const outputCtx = createChartCanvas('outputTrafficChart');
        if (outputCtx) charts.netOut = makeLineChart(outputCtx, chartData.network.labels || [], chartData.network.output || [], 'rgb(239, 68, 68)', 'Network Output (MB)');
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
                    showApiStatus('success', 'API connected successfully');
                    if (charts.cpu) { charts.cpu.data.labels = data.chartData.cpu.labels || []; charts.cpu.data.datasets[0].data = data.chartData.cpu.data || []; charts.cpu.update(); }
                    if (charts.ram) { charts.ram.data.labels = data.chartData.memory.labels || []; charts.ram.data.datasets[0].data = data.chartData.memory.data || []; charts.ram.update(); }
                    if (charts.disk) { charts.disk.data.labels = data.chartData.disk.labels || []; charts.disk.data.datasets[0].data = data.chartData.disk.data || []; charts.disk.update(); }
                    pushNetSample(data.summary.network_in, data.summary.network_out);
                    if (charts.netIn) { charts.netIn.data.labels = netLabels; charts.netIn.data.datasets[0].data = netInput; charts.netIn.update(); }
                    if (charts.netOut) { charts.netOut.data.labels = netLabels; charts.netOut.data.datasets[0].data = netOutput; charts.netOut.update(); }
                    const summary = data.summary || {};
                    let status = 'healthy';
                    const cpu = Number(summary.cpu_usage || 0);
                    const mem = Number(summary.memory_percent || 0);
                    const load1 = Number(summary.load_avg_1 || 0);
                    if (cpu > 90 || mem > 90 || load1 > 4) status = 'critical';
                    else if (cpu > 70 || mem > 70 || load1 > 2) status = 'warning';
                    const statusTextEl = document.getElementById('serverStatusText');
                    const statusDotEl = document.getElementById('serverStatusDot');
                    if (statusTextEl) {
                        const textClasses = { healthy: 'text-green-600', warning: 'text-yellow-600', critical: 'text-red-600', unknown: 'text-gray-600' };
                        const textMap = { healthy: 'Running', warning: 'Warning', critical: 'Critical', unknown: 'Unknown' };
                        statusTextEl.className = `${textClasses[status]} font-medium`;
                        statusTextEl.textContent = textMap[status];
                    }
                    if (statusDotEl) {
                        const bgClasses = { healthy: 'bg-green-500', warning: 'bg-yellow-500', critical: 'bg-red-500', unknown: 'bg-gray-500' };
                        statusDotEl.className = `h-3 w-3 rounded-full ${bgClasses[status]}`;
                    }
                    setText('[data-cpu]', (data.summary.cpu_usage ?? '--') + '%');
                    setText('[data-memory]', (data.summary.memory_percent ?? '--') + '%');
                    setText('[data-disk]', (data.summary.disk_percent ?? '--') + '%');
                    setText('loadAvgText', 
                        (summary.load_avg_1 ?? '--') + ', ' + 
                        (summary.load_avg_5 ?? '--') + ', ' + 
                        (summary.load_avg_15 ?? '--'));
                    const now = new Date();
                    setText('last-updated-time',
                        now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' +
                        now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }));
                    setText('metrics-updated',
                        now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
                }
            } catch (error) {
                console.error('Update failed:', error);
                showApiStatus('error', 'Network error - cannot connect to API');
            } finally {
                isUpdating = false;
            }
        }
        // Expose refresh function globally
        window.refreshLinuxData = updateDashboard;

        setInterval(updateDashboard, 30000);
    });
</script>
@endpush
