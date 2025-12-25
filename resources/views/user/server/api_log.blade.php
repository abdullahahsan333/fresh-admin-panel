@extends('layouts.user')

@section('content')
<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">API Log</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: {{ now()->format('M d, H:i') }}</div>
    </div>
</header>

<!-- Top Row: 4 Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Request Count -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-[rgb(var(--color-primary)/.12)] flex items-center justify-center text-[rgb(var(--color-primary))]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Request Count</div>
        <div class="text-xs text-gray-500">Total number of Requests</div>
    </div>

    <!-- Requests / Min -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-yellow-100 flex items-center justify-center text-yellow-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Requests / Min</div>
        <div class="text-xs text-gray-500">Avg Requests per minute</div>
    </div>

    <!-- Failed Requests -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-red-100 flex items-center justify-center text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Failed Requests</div>
        <div class="text-xs text-gray-500">Number of failed requests</div>
    </div>

    <!-- Response Time -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-2">
            <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-800">--</span>
        </div>
        <div class="text-sm font-medium text-gray-700">Response Time</div>
        <div class="text-xs text-gray-500">Average Response Time</div>
    </div>
</div>

<!-- Main Content Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-16rem)] min-h-[600px]">
    
    <!-- Left Column: API Endpoints List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 flex flex-col lg:col-span-1 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 mb-1">API Endpoints</h2>
            <div class="text-xs text-gray-500 mb-3">Browse logged API endpoints</div>
            <div class="flex gap-2">
                <input type="text" placeholder="Search endpoints, methods, status, IP..." class="flex-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
                <button class="bg-[rgb(var(--color-primary)/.90)] hover:bg-[rgb(var(--color-primary))] text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">Search</button>
            </div>
        </div>
        
        <div class="overflow-y-auto flex-1 p-2" id="apiEndpointList">
            <!-- Example Group -->
            <div class="mb-2">
                <div class="flex items-center gap-2 px-2 py-1.5 hover:bg-gray-50 rounded cursor-pointer group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">GET</span>
                    <span class="text-sm font-medium text-gray-700">GET Endpoints</span>
                </div>
                
                <div class="pl-4 mt-1 space-y-1">
                    <!-- Active Item -->
                    <div class="p-2 hover:bg-gray-50 rounded border-l-2 border-transparent cursor-pointer endpoint-item" data-id="ep1">
                        <div class="flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-700 truncate">/api/user/profileinlive</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-gray-500">
                            <span>08:18:40 PM ::1</span>
                            <div class="flex gap-2">
                                <span class="font-bold text-green-600">200</span>
                                <span>50.21ms</span>
                            </div>
                        </div>
                    </div>

                    <!-- Other Items -->
                    <div class="p-2 hover:bg-gray-50 rounded border-l-2 border-transparent cursor-pointer endpoint-item" data-id="ep2">
                        <div class="flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600 truncate">/api/user/profileinlive</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-gray-400">
                            <span>08:17:54 PM ::ffff:127.0.0.1</span>
                            <div class="flex gap-2">
                                <span class="font-bold text-green-600">200</span>
                                <span>48.73ms</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-2 hover:bg-gray-50 rounded border-l-2 border-transparent cursor-pointer endpoint-item" data-id="ep3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600 truncate">/api/user/profileinlive</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-gray-400">
                            <span>08:17:40 PM ::ffff:127.0.0.1</span>
                            <div class="flex gap-2">
                                <span class="font-bold text-green-600">200</span>
                                <span>75.31ms</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- More placeholders to fill space -->
                        <div class="p-2 hover:bg-gray-50 rounded border-l-2 border-transparent cursor-pointer endpoint-item" data-id="ep4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600 truncate">/api/user/profileinlive</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-gray-400">
                            <span>08:16:51 PM ::ffff:127.0.0.1</span>
                            <div class="flex gap-2">
                                <span class="font-bold text-green-600">200</span>
                                <span>48.15ms</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Request Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 flex flex-col lg:col-span-2 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 mb-1">Request Details</h2>
            <div class="text-sm text-gray-500 flex items-center gap-2" id="requestHeader">
                <span class="font-mono text-gray-700">No endpoint selected</span>
            </div>
        </div>
        
        <div class="border-b border-gray-200">
            <nav class="flex space-x-6 px-6" aria-label="Tabs" id="detailsTabs">
                <button data-tab="request" class="border-gray-800 text-gray-800 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Request</button>
                <button data-tab="response" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Response</button>
                <button data-tab="headers" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Headers</button>
                <button data-tab="metadata" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Metadata</button>
            </nav>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50" id="detailsContent">
            <div class="flex items-center justify-center h-full text-sm text-gray-500">Select an API endpoint to view details</div>
        </div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script>
    const cp = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim();
    const themeColor = `rgb(${cp.replace(/\s+/g, ',')})`;
    const endpointData = {
        ep1: {
            method: 'GET',
            path: '/api/user/profileinlive',
            time: '8:18:40 PM',
            status: 200,
            latency: '50.21ms',
            request: {
                url: 'api.heylivo.com/api/user/profileinlive?user_id=1005624&member_id=1034643',
                body: { headers: { Accept: 'application/json', 'User-Agent': 'axios/1.7.7' } }
            },
            response: { status: 200, message: 'OK', data: {} },
            headers: { 'Content-Type': 'application/json', 'X-Request-ID': 'abc-123' },
            metadata: { ip: '::1', userId: 1005624, memberId: 1034643 }
        },
        ep2: {
            method: 'GET',
            path: '/api/user/profileinlive',
            time: '8:17:54 PM',
            status: 200,
            latency: '48.73ms',
            request: { url: 'api.heylivo.com/api/user/profileinlive', body: {} },
            response: { status: 200, message: 'OK', data: {} },
            headers: { 'Content-Type': 'application/json' },
            metadata: { ip: '::ffff:127.0.0.1' }
        },
        ep3: {
            method: 'GET',
            path: '/api/user/profileinlive',
            time: '8:17:40 PM',
            status: 200,
            latency: '75.31ms',
            request: { url: 'api.heylivo.com/api/user/profileinlive', body: {} },
            response: { status: 200, message: 'OK', data: {} },
            headers: { 'Content-Type': 'application/json' },
            metadata: { ip: '::ffff:127.0.0.1' }
        },
        ep4: {
            method: 'GET',
            path: '/api/user/profileinlive',
            time: '8:16:51 PM',
            status: 200,
            latency: '48.15ms',
            request: { url: 'api.heylivo.com/api/user/profileinlive', body: {} },
            response: { status: 200, message: 'OK', data: {} },
            headers: { 'Content-Type': 'application/json' },
            metadata: { ip: '::ffff:127.0.0.1' }
        }
    };
    let activeEndpointId = null;
    let activeTab = 'request';
    const headerEl = document.getElementById('requestHeader');
    const contentEl = document.getElementById('detailsContent');
    const tabNav = document.getElementById('detailsTabs');
    const listEl = document.getElementById('apiEndpointList');
    function renderHeader() {
        if (!activeEndpointId) {
            headerEl.innerHTML = '<span class="font-mono text-gray-700">No endpoint selected</span>';
            return;
        }
        const ep = endpointData[activeEndpointId];
        headerEl.innerHTML = `
            <span class="font-mono text-gray-700">${ep.method} > ${ep.path}</span>
            <span class="text-gray-400">&gt;</span>
            <span>${ep.time}</span>
            <span class="text-gray-400">&middot;</span>
            <span class="font-mono ${ep.status >= 200 && ep.status < 300 ? 'text-green-600' : 'text-red-600'}">${ep.status}</span>
            <span class="text-gray-400">&middot;</span>
            <span class="font-mono text-gray-700">${ep.latency}</span>
        `;
    }
    function jsonBlock(label, value) {
        return `
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">${label}</label>
                <pre class="bg-white border border-gray-200 rounded p-2 text-xs font-mono text-gray-700 whitespace-pre-wrap shadow-sm">${typeof value === 'string' ? value : JSON.stringify(value, null, 2)}</pre>
            </div>
        `;
    }
    function renderContent() {
        if (!activeEndpointId) {
            contentEl.innerHTML = '<div class="flex items-center justify-center h-full text-sm text-gray-500">Select an API endpoint to view details</div>';
            return;
        }
        const ep = endpointData[activeEndpointId];
        if (activeTab === 'request') {
            contentEl.innerHTML = `
                ${jsonBlock('Request URL', ep.request.url)}
                ${jsonBlock('Request Body', ep.request.body)}
            `;
        } else if (activeTab === 'response') {
            contentEl.innerHTML = `
                ${jsonBlock('Response Body', ep.response)}
            `;
        } else if (activeTab === 'headers') {
            contentEl.innerHTML = `
                ${jsonBlock('Headers', ep.headers)}
            `;
        } else if (activeTab === 'metadata') {
            contentEl.innerHTML = `
                ${jsonBlock('Metadata', ep.metadata)}
            `;
        }
    }
    function setActiveTab(tab) {
        activeTab = tab;
        tabNav.querySelectorAll('button').forEach(btn => {
            if (btn.dataset.tab === tab) {
                btn.className = 'border-gray-800 text-gray-800 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm';
            } else {
                btn.className = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm';
            }
        });
        renderContent();
    }
    tabNav.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-tab]');
        if (!btn) return;
        e.preventDefault();
        setActiveTab(btn.dataset.tab);
    });
    listEl.addEventListener('click', (e) => {
        const item = e.target.closest('.endpoint-item');
        if (!item) return;
        activeEndpointId = item.dataset.id;
        listEl.querySelectorAll('.endpoint-item').forEach(el => {
            el.classList.remove('bg-blue-50', 'border-blue-500');
            el.classList.add('border-transparent');
        });
        item.classList.add('bg-blue-50', 'border-blue-500');
        item.classList.remove('border-transparent');
        renderHeader();
        setActiveTab('request');
    });
    renderHeader();
    renderContent();
</script>
@endpush
