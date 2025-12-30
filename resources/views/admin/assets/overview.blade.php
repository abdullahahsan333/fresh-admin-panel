@extends('layouts.admin')

@section('content')
<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Assets Overview</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500" id="last-updated">Last updated: {{ now()->format('M d, H:i') }}</div>
        <button onclick="refreshAllData()" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
            Refresh All
        </button>
    </div>
</header>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-700 font-medium">Loading data...</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- 1. Server List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Server List</h2>
            <div class="relative">
                <button onclick="toggleDropdown('servers-dropdown')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
                <div id="servers-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10 hidden">
                    <div class="py-1">
                        <a href="javascript:void(0);" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Refresh</a>
                        <a href="{{ route('admin.assets.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage Servers</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-2">Server IP</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">CPU</th>
                        <th class="px-3 py-2">RAM</th>
                    </tr>
                </thead>
                <tbody id="server-list-body" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-xs text-gray-500 text-center">
            Showing <span id="server-count">0</span> servers
        </div>
    </div>

    <!-- 2. Status Cards Grid -->
    <div class="grid grid-cols-2 gap-4">
        <!-- MySQL -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg text-gray-800">MySQL</h3>
                    <div class="status-indicator" id="mysql-status-indicator"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1" id="mysql-updated">--</p>
            </div>
            <div class="mt-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Connections:</span>
                    <span id="mysql-connections" class="font-medium">--</span>
                </div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Queries/sec:</span>
                    <span id="mysql-queries" class="font-medium">--</span>
                </div>
            </div>
        </div>
        
        <!-- Redis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg text-gray-800">Redis</h3>
                    <div class="status-indicator" id="redis-status-indicator"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1" id="redis-updated">--</p>
            </div>
            <div class="mt-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Memory:</span>
                    <span id="redis-memory" class="font-medium">--</span>
                </div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Ops/sec:</span>
                    <span id="redis-ops" class="font-medium">--</span>
                </div>
            </div>
        </div>
        
        <!-- MongoDB -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg text-gray-800">MongoDB</h3>
                    <div class="status-indicator" id="mongodb-status-indicator"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1" id="mongodb-updated">--</p>
            </div>
            <div class="mt-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Connections:</span>
                    <span id="mongodb-connections" class="font-medium">--</span>
                </div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Ops/sec:</span>
                    <span id="mongodb-ops" class="font-medium">--</span>
                </div>
            </div>
        </div>
        
        <!-- Server Health -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg text-gray-800">Server Health</h3>
                    <div class="status-indicator" id="server-status-indicator"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1" id="server-updated">--</p>
            </div>
            <div class="mt-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Load Avg:</span>
                    <span id="server-load" class="font-medium">--</span>
                </div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Uptime:</span>
                    <span id="server-uptime" class="font-medium">--</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MySQL Connection Usage -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-semibold text-gray-800">MySQL Connection Usage</h2>
            <button onclick="refreshMySQLChart()" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        
        <div class="flex-1 flex flex-col items-center justify-center">
            <!-- Connection Usage Chart -->
            <div class="relative h-40 w-40 mb-6">
                <svg class="h-full w-full transform -rotate-90" viewBox="0 0 36 36">
                    <!-- Background Circle -->
                    <path class="text-gray-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
                    <!-- Progress Circle -->
                    <path id="mysql-chart-progress" class="text-blue-500" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center flex-col">
                    <span id="mysql-chart-percent" class="text-2xl font-bold text-gray-800">0%</span>
                    <span class="text-xs text-gray-500 text-center px-2">Connection Usage</span>
                </div>
            </div>
            
            <div class="flex items-center gap-8 w-full px-4">
                <div class="flex items-start gap-2">
                    <div class="w-3 h-3 rounded bg-blue-100 mt-1"></div>
                    <div>
                        <div class="text-xs text-gray-500">Current Connections</div>
                        <div id="mysql-current-connections" class="font-bold text-gray-800">0</div>
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <div class="w-3 h-3 rounded bg-gray-100 mt-1"></div>
                    <div>
                        <div class="text-xs text-gray-500">Max Connections</div>
                        <div id="mysql-max-connections" class="font-bold text-gray-800">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- 4. SSL Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-semibold text-gray-800">SSL Information</h2>
            <button onclick="refreshSSLInfo()" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="text-sm text-gray-500 mb-6" id="ssl-days-left">-- days left</div>

        <div class="space-y-4">
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-600">Valid</span>
                </div>
                <span id="ssl-valid" class="font-medium text-gray-800">--</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-600">Issued On</span>
                </div>
                <span id="ssl-issued" class="font-medium text-gray-800">--</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-600">Expires On</span>
                </div>
                <span id="ssl-expires" class="font-medium text-gray-800">--</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    <span class="text-gray-600">Domain</span>
                </div>
                <span id="ssl-domain" class="font-medium text-gray-800">--</span>
            </div>
        </div>
    </div>

    <!-- 5. Top Resource Consumers -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Top Resource Consumers</h2>
            <button onclick="refreshTopProcesses()" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-3">Process</th>
                        <th class="px-3 py-3">CPU</th>
                        <th class="px-3 py-3">MEM</th>
                        <th class="px-3 py-3">PID</th>
                    </tr>
                </thead>
                <tbody id="top-processes-body" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-xs text-gray-500 text-center">
            <span id="processes-count">0</span> active processes
        </div>
    </div>

    <!-- 6. Schedulers -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Schedulers</h2>
            <button onclick="refreshSchedulerData()" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-3">Job Name</th>
                        <th class="px-3 py-3">Last Run</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Duration</th>
                    </tr>
                </thead>
                <tbody id="scheduler-body" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-xs text-gray-500 text-center">
            <span id="jobs-count">0</span> scheduler jobs
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Configuration
const API_BASE_URL = 'http://157.245.207.91:3001/api';
const APP_NAME = 'livo';

// State management
let activeServerIp = null;
let serversData = [];

// Utility Functions
function showLoading() {
    document.getElementById('loading-overlay').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loading-overlay').classList.add('hidden');
}

function formatTime(date) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function formatUptime(seconds) {
    const days = Math.floor(seconds / 86400);
    const hours = Math.floor((seconds % 86400) / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    
    if (days > 0) return `${days}d ${hours}h ${minutes}m`;
    if (hours > 0) return `${hours}h ${minutes}m`;
    return `${minutes}m`;
}

function getStatusColor(value, type = 'percentage') {
    if (type === 'percentage') {
        if (value >= 80) return 'text-red-500';
        if (value >= 60) return 'text-orange-500';
        return 'text-green-500';
    }
    return 'text-green-500';
}

function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        document.querySelectorAll('[id$="dropdown"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// API Functions
async function fetchServerList() {
    try {
        const response = await fetch(`/admin/assets/server-list`);
        const data = await response.json();
        
        if (data.ok && data.servers) {
            serversData = data.servers;
            updateServerListTable();
            
            // Set active server if not set
            if (!activeServerIp && serversData.length > 0) {
                activeServerIp = serversData[0].ip;
            }
            
            // Fetch detailed data for each service
            if (activeServerIp) {
                fetchServiceData('linux');
                fetchServiceData('mysql');
                fetchServiceData('redis');
                fetchServiceData('mongodb');
                fetchSchedulerData();
                fetchTopProcesses();
            }
        }
    } catch (error) {
        console.error('Error fetching server list:', error);
    }
}

async function fetchServiceData(service) {
    if (!activeServerIp) return;
    
    try {
        const response = await fetch(`/admin/assets/${service}_data/${getServerIdByIp(activeServerIp)}`);
        const data = await response.json();
        
        if (data.ok) {
            updateServiceUI(service, data);
        }
    } catch (error) {
        console.error(`Error fetching ${service} data:`, error);
    }
}

async function fetchSchedulerData() {
    if (!activeServerIp) return;
    
    try {
        const response = await fetch(`/admin/assets/scheduler_data/${getServerIdByIp(activeServerIp)}?minutes=15`);
        const data = await response.json();
        
        if (data.ok) {
            updateSchedulerTable(data.logs || []);
        }
    } catch (error) {
        console.error('Error fetching scheduler data:', error);
    }
}

async function fetchTopProcesses() {
    if (!activeServerIp) return;
    
    try {
        const response = await fetch(`/admin/assets/linux_data/${getServerIdByIp(activeServerIp)}`);
        const data = await response.json();
        
        if (data.ok && data.topProcesses) {
            updateTopProcessesTable(data.topProcesses);
        }
    } catch (error) {
        console.error('Error fetching top processes:', error);
    }
}

async function fetchSSLInfo(domain = 'api.heylivo.com') {
    try {
        // This would call your SSL checking API
        // For now, we'll simulate with mock data
        const mockSSLData = {
            valid: true,
            issued: '2024-01-01',
            expires: '2025-12-31',
            domain: domain,
            daysLeft: 365
        };
        
        updateSSLInfo(mockSSLData);
    } catch (error) {
        console.error('Error fetching SSL info:', error);
    }
}

// UI Update Functions
function updateServerListTable() {
    const tbody = document.getElementById('server-list-body');
    const countElement = document.getElementById('server-count');
    
    tbody.innerHTML = '';
    countElement.textContent = serversData.length;
    
    serversData.forEach(server => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 cursor-pointer';
        row.onclick = () => selectServer(server.ip);
        
        // Determine status based on last update time
        const lastUpdated = new Date(server.last_updated || server.created_at);
        const minutesAgo = (Date.now() - lastUpdated.getTime()) / (1000 * 60);
        const isOnline = minutesAgo < 5;
        
        row.innerHTML = `
            <td class="px-3 py-3 font-medium text-gray-800">
                ${server.ip}
                ${server.ip === activeServerIp ? '<span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Active</span>' : ''}
            </td>
            <td class="px-3 py-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isOnline ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    <span class="w-2 h-2 rounded-full ${isOnline ? 'bg-green-500' : 'bg-red-500'} mr-1"></span>
                    ${isOnline ? 'Online' : 'Offline'}
                </span>
            </td>
            <td class="px-3 py-3">
                <span class="${getStatusColor(server.cpu_usage || 0)} font-medium">
                    ${(server.cpu_usage || 0).toFixed(1)}%
                </span>
            </td>
            <td class="px-3 py-3">
                <span class="${getStatusColor(server.memory_usage || 0)} font-medium">
                    ${(server.memory_usage || 0).toFixed(1)}%
                </span>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function selectServer(ip) {
    activeServerIp = ip;
    updateServerListTable();
    
    // Refresh all data for the selected server
    refreshAllData();
}

function getServerIdByIp(ip) {
    const server = serversData.find(s => s.ip === ip);
    return server ? server.id : 1; // Default to first server if not found
}

function updateServiceUI(service, data) {
    const serviceMap = {
        'linux': {
            indicator: 'server-status-indicator',
            updated: 'server-updated',
            connections: 'server-load',
            ops: 'server-uptime',
            chart: null
        },
        'mysql': {
            indicator: 'mysql-status-indicator',
            updated: 'mysql-updated',
            connections: 'mysql-connections',
            ops: 'mysql-queries',
            chart: true
        },
        'redis': {
            indicator: 'redis-status-indicator',
            updated: 'redis-updated',
            connections: 'redis-memory',
            ops: 'redis-ops',
            chart: null
        },
        'mongodb': {
            indicator: 'mongodb-status-indicator',
            updated: 'mongodb-updated',
            connections: 'mongodb-connections',
            ops: 'mongodb-ops',
            chart: null
        }
    };
    
    const config = serviceMap[service];
    if (!config) return;
    
    // Update status indicator
    const indicator = document.getElementById(config.indicator);
    if (indicator) {
        indicator.innerHTML = data.apiStatus?.connected ? 
            '<span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>' :
            '<span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>';
    }
    
    // Update timestamp
    const updatedEl = document.getElementById(config.updated);
    if (updatedEl) {
        updatedEl.textContent = new Date().toLocaleTimeString();
    }
    
    // Update connections/usage
    if (config.connections) {
        const connectionsEl = document.getElementById(config.connections);
        if (connectionsEl) {
            if (service === 'linux') {
                connectionsEl.textContent = data.summary?.load_avg_1?.toFixed(2) || '--';
            } else if (service === 'redis') {
                connectionsEl.textContent = data.summary?.used_memory ? formatBytes(data.summary.used_memory * 1024 * 1024) : '--';
            } else {
                connectionsEl.textContent = data.summary?.connections_used || data.summary?.connected_clients || data.summary?.connections_current || '--';
            }
        }
    }
    
    // Update ops/queries
    if (config.ops) {
        const opsEl = document.getElementById(config.ops);
        if (opsEl) {
            if (service === 'linux') {
                opsEl.textContent = data.summary?.uptime_seconds ? formatUptime(data.summary.uptime_seconds) : '--';
            } else if (service === 'mysql') {
                opsEl.textContent = data.summary?.queries_per_second?.toFixed(2) || '--';
            } else if (service === 'redis') {
                opsEl.textContent = data.summary?.instantaneous_ops_per_sec?.toFixed(2) || '--';
            } else if (service === 'mongodb') {
                opsEl.textContent = data.summary?.operations_per_second?.toFixed(2) || '--';
            }
        }
    }
    
    // Update MySQL chart if applicable
    if (config.chart && data.summary) {
        updateMySQLChart(data.summary);
    }
}

function updateMySQLChart(summary) {
    const current = summary.connections_used || 0;
    const max = summary.connections || 0;
    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
    
    // Update chart
    const progress = document.getElementById('mysql-chart-progress');
    if (progress) {
        progress.style.strokeDasharray = `${percent}, 100`;
        progress.className = `text-${percent >= 80 ? 'red' : percent >= 60 ? 'orange' : 'blue'}-500`;
    }
    
    // Update text
    document.getElementById('mysql-chart-percent').textContent = `${percent}%`;
    document.getElementById('mysql-current-connections').textContent = current;
    document.getElementById('mysql-max-connections').textContent = max;
}

function updateTopProcessesTable(processes) {
    const tbody = document.getElementById('top-processes-body');
    const countElement = document.getElementById('processes-count');
    
    tbody.innerHTML = '';
    countElement.textContent = processes.length;
    
    processes.slice(0, 5).forEach(proc => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-3 py-3 font-medium text-gray-800">${proc.name || 'Unknown'}</td>
            <td class="px-3 py-3">
                <span class="${getStatusColor(proc.cpu || 0)} font-medium">
                    ${(proc.cpu || 0).toFixed(1)}%
                </span>
            </td>
            <td class="px-3 py-3">
                <span class="${getStatusColor(proc.mem || 0, 'memory')} font-medium">
                    ${(proc.mem || 0).toFixed(1)} MB
                </span>
            </td>
            <td class="px-3 py-3 text-gray-500">${proc.pid || 'N/A'}</td>
        `;
        tbody.appendChild(row);
    });
}

function updateSchedulerTable(logs) {
    const tbody = document.getElementById('scheduler-body');
    const countElement = document.getElementById('jobs-count');
    
    tbody.innerHTML = '';
    countElement.textContent = logs.length;
    
    // Group logs by job name
    const jobs = {};
    logs.forEach(log => {
        const jobName = log.job_name || log.name || 'Unknown Job';
        if (!jobs[jobName]) {
            jobs[jobName] = {
                lastRun: log.timestamp || log.run_at,
                status: log.status || 'unknown',
                duration: log.duration_ms || 0
            };
        }
    });
    
    Object.entries(jobs).slice(0, 5).forEach(([name, data]) => {
        const row = document.createElement('tr');
        const timeAgo = data.lastRun ? getTimeAgo(new Date(data.lastRun)) : 'Never';
        
        row.innerHTML = `
            <td class="px-3 py-3 font-medium text-gray-800">${name}</td>
            <td class="px-3 py-3 text-gray-600">${timeAgo}</td>
            <td class="px-3 py-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.status === 'success' ? 'bg-green-100 text-green-800' : data.status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'}">
                    ${data.status || 'unknown'}
                </span>
            </td>
            <td class="px-3 py-3 text-gray-600">${data.duration ? `${data.duration}ms` : 'N/A'}</td>
        `;
        tbody.appendChild(row);
    });
}

function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval >= 1) return interval + " years ago";
    
    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) return interval + " months ago";
    
    interval = Math.floor(seconds / 86400);
    if (interval >= 1) return interval + " days ago";
    
    interval = Math.floor(seconds / 3600);
    if (interval >= 1) return interval + " hours ago";
    
    interval = Math.floor(seconds / 60);
    if (interval >= 1) return interval + " minutes ago";
    
    return Math.floor(seconds) + " seconds ago";
}

function updateSSLInfo(data) {
    document.getElementById('ssl-valid').textContent = data.valid ? 'Yes' : 'No';
    document.getElementById('ssl-issued').textContent = new Date(data.issued).toLocaleDateString();
    document.getElementById('ssl-expires').textContent = new Date(data.expires).toLocaleDateString();
    document.getElementById('ssl-domain').textContent = data.domain;
    document.getElementById('ssl-days-left').textContent = `${data.daysLeft} days left`;
}

// Refresh Functions
function refreshAllData() {
    showLoading();
    fetchServerList().finally(() => {
        setTimeout(() => {
            hideLoading();
            updateLastUpdated();
        }, 500);
    });
}

function refreshMySQLChart() {
    if (activeServerIp) {
        fetchServiceData('mysql');
    }
}

function refreshTopProcesses() {
    if (activeServerIp) {
        fetchTopProcesses();
    }
}

function refreshSchedulerData() {
    if (activeServerIp) {
        fetchSchedulerData();
    }
}

function refreshSSLInfo() {
    fetchSSLInfo();
}

function updateLastUpdated() {
    document.getElementById('last-updated').textContent = 
        `Last updated: ${new Date().toLocaleString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        })}`;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set initial active server
    refreshAllData();
    
    // Auto-refresh every 30 seconds
    setInterval(() => {
        if (activeServerIp) {
            fetchServerList();
        }
    }, 30000);
});
</script>

<style>
.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.table tr:hover {
    background-color: #f9fafb;
}

/* CSS for the donut chart */
#mysql-chart-progress {
    transition: stroke-dasharray 0.5s ease;
}
</style>
@endpush