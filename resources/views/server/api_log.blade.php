@extends($layout ?? 'layouts.admin')

@section('content')
<!-- Header Section -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div>
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-bold text-gray-900">API Log</h2>
            <div id="apiStatusDot" class="hidden">
                <span class="inline-block w-2 h-2 rounded-full"></span>
            </div>
        </div>
        <p class="text-sm text-gray-600 mt-1">Server: {{ $server->ip }} • Last updated: <span id="last-updated">{{ now()->format('M d, H:i') }}</span></p>
    </div>
    <button id="refresh-btn" class="px-4 py-2 text-blue-600 rounded-lg flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
    </button>
</header>

<div id="error-banner" class="hidden mx-6 -mt-4 mb-4 bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-md text-sm"></div>

<div class="flex flex-col gap-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Request Count -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Request Count</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2" id="request-count">{{ $summary['request_count'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-200 flex items-center justify-center">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M393 341.6V206c0-14.7 0-42.1-9.4-65.3-11.9-29.2-36-44.7-69.6-44.7h-77.7L276 55.8c5.4-5.4 5.4-14.3 0-19.8l-.1-.1c-2.7-2.5-6.2-3.9-9.8-3.9-3.8 0-7.3 1.4-9.9 4.1l-54.8 54.6C192.9 61.5 165.9 40 134 40c-38.6 0-70 31.4-70 70 0 17 6.2 33.3 17.3 46.1 9.9 11.3 23.1 19.1 37.7 22.3v163.3c-14.5 3.2-27.8 11-37.7 22.3C70.2 376.7 64 393 64 410c0 38.6 31.4 70 70 70s70-31.4 70-70c0-17.4-6.4-34-18-46.9-10.3-11.4-24-19.1-39-21.9V178.8c15-2.8 28.7-10.5 39-21.9 7.6-8.4 12.9-18.4 15.7-29.1l56.4 56.2c2.6 2.6 6.1 4.1 9.9 4.1 3.7 0 7.3-1.4 9.9-4.1 2.6-2.6 4.1-6.1 4.1-9.9 0-3.7-1.4-7.3-4.1-9.9l-.1-.1-41.1-40.1H314c20.4 0 33.6 7.5 41.6 23.5 8.4 17 9.4 41.5 9.4 58.5v135.2c-15 2.8-28.7 10.5-39 21.9-11.6 12.9-18 29.5-18 46.9 0 38.6 31.4 70 70 70s70-31.4 70-70c0-17-6.2-33.3-17.3-46.1-9.9-11.3-23.2-19.1-37.7-22.3zM176 410c0 23.2-18.8 42-42 42s-42-18.8-42-42 18.8-42 42-42 42 18.8 42 42zm-42-258c-23.2 0-42-18.8-42-42s18.8-42 42-42 42 18.8 42 42-18.8 42-42 42zm244 300c-23.2 0-42-18.8-42-42s18.8-42 42-42 42 18.8 42 42-18.8 42-42 42z"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Total number of Requests</p>
        </div>

        <!-- Requests / Min -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Requests / Min</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2"><span id="requests-per-min">{{ $summary['requests_per_min'] }}</span>/min</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-yellow-200 flex items-center justify-center">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2.99994 10V14L3.99994 15H12.9999L13.9999 14V5L13.7099 4.29L10.7099 1.29L9.99994 1H8V2H9.99994L12.9999 5V14H3.99994V10H2.99994ZM11 6H9V4H8V6H6V7H8V9H9V7H11V6ZM6 11H11V12H6V11Z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M7.06065 3.85356L4.91421 6L4.2071 5.29289L5.49999 4H2.5C2.10218 4 1.72064 4.15804 1.43934 4.43934C1.15804 4.72065 1 5.10218 1 5.5C1 5.89783 1.15804 6.27936 1.43934 6.56066C1.72064 6.84197 2.10218 7 2.5 7H3V8H2.5C1.83696 8 1.20107 7.73661 0.732233 7.26777C0.263392 6.79893 0 6.16305 0 5.5C0 4.83696 0.263392 4.20108 0.732233 3.73224C1.20107 3.2634 1.83696 3 2.5 3H5.49999L4.2071 1.70711L4.91421 1L7.06065 3.14645L7.06065 3.85356Z"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Avg Requests per minute</p>
        </div>

        <!-- Failed Requests -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed Requests</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2" id="failed-requests">{{ $summary['failed_requests'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-red-200 flex items-center justify-center">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M22.266 2.711a.75.75 0 1 0-1.061-1.06l-1.983 1.983-1.984-1.983a.75.75 0 1 0-1.06 1.06l1.983 1.983-1.983 1.984a.75.75 0 0 0 1.06 1.06l1.984-1.983 1.983 1.983a.75.75 0 0 0 1.06-1.06l-1.983-1.984 1.984-1.983ZM4.75 1.5a3.25 3.25 0 0 1 .745 6.414A.827.827 0 0 1 5.5 8v8a.827.827 0 0 1-.005.086A3.25 3.25 0 0 1 4.75 22.5a3.25 3.25 0 0 1-.745-6.414A.827.827 0 0 1 4 16V8c0-.029.002-.057.005-.086A3.25 3.25 0 0 1 4.75 1.5ZM16 19.25a3.252 3.252 0 0 1 2.5-3.163V9.625a.75.75 0 0 1 1.5 0v6.462a3.252 3.252 0 0 1-.75 6.413A3.25 3.25 0 0 1 16 19.25ZM3 4.75a1.75 1.75 0 1 0 3.501-.001A1.75 1.75 0 0 0 3 4.75Zm0 14.5a1.75 1.75 0 1 0 3.501-.001A1.75 1.75 0 0 0 3 19.25Zm16.25-1.75a1.75 1.75 0 1 0 .001 3.501 1.75 1.75 0 0 0-.001-3.501Z"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Number of failed requests</p>
        </div>

        <!-- Response Time -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Response Time</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2"><span id="avg-response-time">{{ $summary['avg_response_time'] }}</span>ms</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-green-200 flex items-center justify-center">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M232.9 371.6c0 12.7 10.4 23.1 23.1 23.1s23.1-10.4 23.1-23.1c0-12.7-10.4-23.1-23.1-23.1s-23.1 10.3-23.1 23.1zm0-323.6v92.4h46.2V96.1c78.3 11.3 138.7 78.3 138.7 159.9 0 89.4-72.3 161.8-161.8 161.8S94.2 345.4 94.2 256c0-38.8 13.6-74.4 36.5-102.2L256 279.1l32.6-32.6L131.4 89.4v.5C80.8 127.7 48 187.8 48 256c0 114.9 92.9 208 208 208 114.9 0 208-93.1 208-208S370.9 48 256 48h-23.1zm161.8 208c0-12.7-10.4-23.1-23.1-23.1-12.7 0-23.1 10.4-23.1 23.1s10.4 23.1 23.1 23.1c12.7 0 23.1-10.4 23.1-23.1zm-277.4 0c0 12.7 10.4 23.1 23.1 23.1s23.1-10.4 23.1-23.1-10.4-23.1-23.1-23.1-23.1 10.4-23.1 23.1z"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Average Response Time</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: API Endpoints -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">API Endpoints</h2>
                    <p class="text-sm text-gray-600 mt-1">Browse logged API endpoints</p>

                    <!-- Search -->
                    <div class="mt-4">
                        <div class="relative">
                            <input type="text" 
                                   id="search-input"
                                   placeholder="Search endpoints, methods, status, IP..." 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <i class="ri-search-line absolute right-3 top-3.5 text-gray-400"></i>
                        </div>
                        <button id="clear-search" class="hidden text-sm text-blue-600 hover:text-blue-800 mt-2">
                            Clear search
                        </button>
                    </div>

                    <!-- Method Tabs -->
                    <div class="mt-4">
                        <div class="flex space-x-1 border-b border-gray-200" id="method-tabs-container">
                            <!-- Method tabs will be dynamically inserted here -->
                        </div>
                    </div>
                </div>

                <!-- Endpoints List -->
                <div class="overflow-y-auto h-100 p-4" id="api-endpoint-list">
                    <div id="method-content-container"></div>
                </div>
            </div>
        </div>

        <!-- Right Column: Request Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Request Details</h2>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mt-1" id="request-header">
                        @if($firstLog)
                            <span class="font-medium text-gray-900">{{ $firstLog['method'] ?? 'GET' }}</span>
                            <span class="text-gray-400">></span>
                            <span class="font-mono">{{ extractApiEndpoint($firstLog['url'] ?? '') }}</span>
                            <span class="text-gray-400">></span>
                            <span>{{ formatApiTimestamp($firstLog['date'] ?? '') }}</span>
                        @else
                            <span class="text-gray-500">No endpoint selected</span>
                        @endif
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px" id="detail-tabs">
                        <button class="detail-tab-btn px-6 py-3 border-b-2 font-medium text-sm border-blue-600 text-blue-600 flex items-center gap-2"
                                data-tab="request">
                            <i class="ri-send-plane-line"></i>
                            Request
                        </button>
                        <button class="detail-tab-btn px-6 py-3 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center gap-2"
                                data-tab="response">
                            <i class="ri-reply-line"></i>
                            Response
                        </button>
                        <button class="detail-tab-btn px-6 py-3 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center gap-2"
                                data-tab="headers">
                            <i class="ri-file-list-3-line"></i>
                            Headers
                        </button>
                        <button class="detail-tab-btn px-6 py-3 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center gap-2"
                                data-tab="metadata">
                            <i class="ri-information-line"></i>
                            Metadata
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6 overflow-y-auto h-100" id="tab-content-container">
                    <!-- Request Tab -->
                    <div id="tab-request" class="tab-content space-y-6">
                        @if($firstLog)
                            <!-- Request URL -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Request URL</h3>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 rounded text-sm font-bold {{ getApiMethodClass($firstLog['method'] ?? 'GET') }}">
                                            {{ $firstLog['method'] ?? 'GET' }}
                                        </span>
                                        <code class="text-sm font-mono text-gray-900 break-all">{{ $firstLog['url'] ?? '' }}</code>
                                    </div>
                                </div>
                            </div>

                            <!-- Request Body -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Request Body</h3>
                                <div class="bg-gray-900 rounded-lg p-4 border border-gray-200 overflow-x-auto">
                                    <pre class="text-sm font-mono text-white whitespace-pre-wrap">{{ json_encode($firstLog['body'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="ri-file-text-line text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Select an API endpoint to view details</p>
                            </div>
                        @endif
                    </div>

                    <!-- Response Tab -->
                    <div id="tab-response" class="tab-content space-y-6 hidden">
                        <!-- Response content will be loaded dynamically -->
                    </div>

                    <!-- Headers Tab -->
                    <div id="tab-headers" class="tab-content space-y-6 hidden">
                        <!-- Headers content will be loaded dynamically -->
                    </div>

                    <!-- Metadata Tab -->
                    <div id="tab-metadata" class="tab-content space-y-6 hidden">
                        <!-- Metadata content will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates for dynamic content -->
<template id="tab-request-template">
    <div class="space-y-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Request URL</h3>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3">
                    <span class="method-badge px-3 py-1 rounded text-sm font-bold"></span>
                    <code class="text-sm font-mono text-gray-900 break-all url-content"></code>
                </div>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Request Body</h3>
            <div class="bg-gray-900 rounded-lg p-4 border border-gray-200 overflow-x-auto">
                <pre class="text-sm font-mono text-white whitespace-pre-wrap body-content"></pre>
            </div>
        </div>
    </div>
</template>

<template id="tab-response-template">
    <div class="space-y-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Response Status</h3>
            <div class="flex items-center gap-4">
                <span class="status-badge px-3 py-1 rounded text-sm font-bold"></span>
                <span class="text-sm text-gray-600 response-time"></span>
                <span class="text-sm text-gray-600 content-length"></span>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Response Body</h3>
            <div class="bg-gray-900 rounded-lg p-4 border border-gray-200 overflow-x-auto">
                <pre class="text-sm font-mono text-white whitespace-pre-wrap response-content"></pre>
            </div>
        </div>
    </div>
</template>

<template id="tab-headers-template">
    <div class="space-y-6">
        <h3 class="text-sm font-semibold text-gray-900">Request & Response Headers</h3>
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Header</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 headers-body">
                    <!-- Headers will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</template>

<template id="tab-metadata-template">
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Request Info -->
            <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="ri-information-line"></i>
                    Request Info
                </h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">IP Address</p>
                        <p class="text-sm font-medium text-gray-900 mt-1 ip-address"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">User Agent</p>
                        <p class="text-sm font-medium text-gray-900 mt-1 user-agent"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Timestamp</p>
                        <p class="text-sm font-medium text-gray-900 mt-1 timestamp"></p>
                    </div>
                </div>
            </div>

            <!-- Performance -->
            <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="ri-dashboard-line"></i>
                    Performance
                </h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Response Time</p>
                        <p class="text-sm font-medium text-gray-900 mt-1 response-time"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Content Length</p>
                        <p class="text-sm font-medium text-gray-900 mt-1 content-length"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status Code</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            <span class="status-badge px-2 py-1 rounded text-xs"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Metadata -->
        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="ri-server-line"></i>
                Additional Details
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">HTTP Version</p>
                    <p class="text-sm font-medium text-gray-900 mt-1 http-version"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Content Type</p>
                    <p class="text-sm font-medium text-gray-900 mt-1 content-type"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Protocol</p>
                    <p class="text-sm font-medium text-gray-900 mt-1 protocol"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Server</p>
                    <p class="text-sm font-medium text-gray-900 mt-1 server-name"></p>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Method content template -->
<template id="method-content-template">
    <div class="method-content" data-method="{method}">
        <!-- Method content will be populated here -->
    </div>
</template>

<!-- Endpoint group template -->
<template id="endpoint-group-template">
    <div class="endpoint-group">
        <div class="endpoint-header flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer">
            <i class="ri-arrow-down-s-line text-gray-400 text-sm toggle-icon"></i>
            <i class="ri-route-line text-indigo-500"></i>
            <span class="text-sm font-medium text-gray-700 truncate">{endpoint}</span>
        </div>
        <div class="logs-container ml-4 mt-1 space-y-1 hidden">
            <!-- Log items will be populated here -->
        </div>
    </div>
</template>

<!-- Log item template -->
<template id="log-item-template">
    <div class="log-item p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-colors"
         data-log-id="{logId}"
         data-log-data="{logData}">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2">
                    <i class="ri-time-line text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-900">{time}</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-xs text-gray-600">{ip}</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-xs text-gray-500 font-mono">{logId}</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-sm font-bold text-gray-800">{status}</span>
                <p class="text-xs text-gray-500 mt-1">{responseTime}ms</p>
            </div>
        </div>
    </div>
</template>

<!-- Empty state template -->
<template id="empty-state-template">
    <div class="text-center py-12">
        <i class="ri-file-text-line text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Select an API endpoint to view details</p>
    </div>
</template>

@endsection

@push('footer_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const panel = "{{ $panel ?? 'admin' }}";
        const dataUrl = "{{ route(($panel ?? 'admin').'.server.api_log.data', $server->id) }}";
        // State
        let currentLog = @json($firstLog ?: null);
        let activeTab = 'request';
        let activeMethod = 'GET';
        let allLogs = [];
        let groupedLogs = @json($groupedLogs ?: (object)[]);

        // Initialize
        initMethodTabs();
        initDetailTabs();
        initSearch();
        initLogSelection();
        initCollapsible();
        
        // Load data once on initial page render; subsequent loads via Refresh button
        fetchData();

        // Set first log as active if exists
        if (currentLog) {
            updateRequestHeader(currentLog);
            updateAllDetailTabs(currentLog);
        } else {
            showEmptyState();
        }

        // Refresh button
        document.getElementById('refresh-btn').addEventListener('click', function() {
            this.classList.add('animate-spin');
            fetchData().finally(() => this.classList.remove('animate-spin'));
        });

        async function fetchData() {
            try {
                const res = await fetch(dataUrl, { 
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status} - ${res.statusText}`);
                }
                
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    throw new Error('Invalid content-type');
                }
                const json = await res.json();
                
                if (!json.ok) {
                    showApiStatus('error', json.message || 'Failed to load API logs');
                    showError(json.message || 'Failed to load API logs. API server may be unreachable.');
                    // Keep existing groupedLogs and UI if API fails
                    updateMethodTabs(groupedLogs);
                    return;
                }
                
                hideError();
                showApiStatus('success', 'API connected successfully');
                updateSummary(json.summary || {});
                groupedLogs = json.groupedLogs || {};
                
                // Store all logs for search
                allLogs = json.logs || [];
                
                // If no logs are available, show empty state
                if (Object.keys(groupedLogs).length === 0 && allLogs.length === 0) {
                    showEmptyState();
                    return;
                }
                
                // Update method tabs and load first method content for visibility
                updateMethodTabs(groupedLogs);
                const methods = Object.keys(groupedLogs);
                if (methods.length === 0) {
                    showEmptyState();
                } else {
                    const firstMethod = methods[0];
                    activeMethod = firstMethod;
                    loadMethodContent(firstMethod, groupedLogs[firstMethod] || {});
                }
                
                // Update last updated time
                document.getElementById('last-updated').textContent = 
                    new Date().toLocaleString([], { 
                        month: 'short', 
                        day: '2-digit', 
                        hour: '2-digit', 
                        minute: '2-digit',
                        hour12: true
                    });
            } catch(e) {
                showError('Failed to load API logs. Please check API server connection.');
                showApiStatus('error', 'Network error - cannot connect to API');
                showEmptyState();
            }
        }

        // Removed unused fetchDataForMethod; all data loads via fetchData()

        function updateSummary(summary) {
            document.getElementById('request-count').textContent = summary.request_count || 0;
            document.getElementById('requests-per-min').textContent = summary.requests_per_min || 0;
            document.getElementById('failed-requests').textContent = summary.failed_requests || 0;
            document.getElementById('avg-response-time').textContent = summary.avg_response_time || 0;
        }

        // Initialize method tabs
        function initMethodTabs() {
            updateMethodTabs(groupedLogs);
        }

        function updateMethodTabs(groupedData) {
            const container = document.getElementById('method-tabs-container');
            const methods = Object.keys(groupedData);
            
            if (methods.length === 0) {
                container.innerHTML = '';
                document.getElementById('api-endpoint-list').innerHTML = `
                    <div class="text-center py-12">
                        <i class="ri-inbox-line text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No logs available</p>
                    </div>`;
                return;
            }

            let tabsHtml = '';
            methods.forEach((method, index) => {
                const isActive = index === 0;
                const methodClass = getMethodClass(method);
                tabsHtml += `
                    <button class="method-tab-btn px-1 py-2 font-medium text-sm whitespace-nowrap ${isActive ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'}"
                            data-method="${method}">
                        <span class="px-2 py-1 rounded text-xs font-bold ${methodClass}">${method}</span>
                    </button>
                `;
            });

            container.innerHTML = tabsHtml;

            // Add event listeners to method tabs
            container.querySelectorAll('.method-tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active tab
                    container.querySelectorAll('.method-tab-btn').forEach(b => {
                        b.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600');
                        b.classList.add('text-gray-500');
                    });
                    
                    this.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                    this.classList.remove('text-gray-500');
                    
                    // Load method content
                    const method = this.dataset.method;
                    activeMethod = method;
                    loadMethodContent(method, groupedData[method] || {});
                });
            });

            // Do not auto-load content; wait for user to click a method
        }

        function loadMethodContent(method, endpoints) {
            const container = document.getElementById('method-content-container');
            let html = '';
            
            for (const [endpoint, logs] of Object.entries(endpoints)) {
                html += `
                    <div class="endpoint-group mb-3">
                        <div class="endpoint-header flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                            <i class="ri-arrow-down-s-line text-gray-400 text-sm toggle-icon"></i>
                            <i class="ri-route-line text-indigo-500"></i>
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs font-mono truncate">${endpoint}</span>
                            <span class="ml-auto px-2 py-1 rounded bg-gray-50 text-gray-500 text-xs">${logs.length} requests</span>
                        </div>
                        <div class="logs-container ml-4 mt-2 space-y-2 hidden">
                `;
                
                for (const logData of logs) {
                    const log = logData.log || {};
                    const logId = logData.id || '';
                    const time = formatTime(log.date || '');
                    const ip = log.ip || 'Unknown';
                    const status = log.status || 0;
                    const responseTime = (log.response_time_ms || log.responseTime || 0).toFixed(2);
                    const logJson = encodeURIComponent(JSON.stringify(log));
                    const methodClass = getMethodClass(log.method || method);
                    const statusClass = getStatusClass(status);
                    
                    html += `
                        <div class="log-item p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-colors"
                            data-log-id="${logId}"
                            data-log-data="${logJson}">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-0.5 rounded text-xs font-bold ${methodClass}">${log.method || method}</span>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">${time}</span>
                                    <span class="text-xs text-gray-500">${ip}</span>
                                </div>
                                <span class="ml-auto px-2 py-0.5 rounded text-xs font-bold ${statusClass}">${status}</span>
                            </div>
                        </div>
                `;
                }
                
                html += `
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = html;
            
            // Re-initialize collapsible for new content
            initCollapsible();
            initLogSelection();
            
            // If no endpoints, show message
            if (Object.keys(endpoints).length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <i class="ri-inbox-line text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No ${method} requests found</p>
                    </div>
                `;
            }
        }

        // Removed auto-select of first log; user clicks a log to view details

        // Initialize detail tabs
        function initDetailTabs() {
            const tabButtons = document.querySelectorAll('.detail-tab-btn');
            
            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active tab button
                    tabButtons.forEach(b => {
                        b.classList.remove('border-blue-600', 'text-blue-600');
                        b.classList.add('border-transparent', 'text-gray-500');
                    });
                    
                    this.classList.add('border-blue-600', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500');
                    
                    // Update active tab
                    activeTab = this.dataset.tab;
                    
                    // Show selected tab and hide others
                    document.querySelectorAll('.tab-content').forEach(tab => {
                        tab.classList.add('hidden');
                    });
                    document.getElementById(`tab-${activeTab}`).classList.remove('hidden');
                    
                    // Update content if we have a current log
                    if (currentLog) {
                        updateTabContent(activeTab, currentLog);
                    } else {
                        showEmptyState();
                    }
                });
            });
        }

        // Initialize search functionality
        function initSearch() {
            const searchInput = document.getElementById('search-input');
            const clearBtn = document.getElementById('clear-search');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                if (searchTerm) {
                    clearBtn.classList.remove('hidden');
                    performSearch(searchTerm);
                } else {
                    clearBtn.classList.add('hidden');
                    resetSearch();
                }
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                this.classList.add('hidden');
                resetSearch();
            });

            function performSearch(term) {
                const logItems = document.querySelectorAll('.log-item');
                let hasVisibleItems = false;

                logItems.forEach(item => {
                    const logData = JSON.parse(decodeURIComponent(item.dataset.logData));
                    const searchText = [
                        logData.method || '',
                        logData.url || '',
                        logData.ip || '',
                        logData.status?.toString() || '',
                        logData.user_agent || '',
                        logData.endpoint || '',
                        formatTime(logData.date || '')
                    ].join(' ').toLowerCase();

                    if (searchText.includes(term)) {
                        item.style.display = 'block';
                        showParentSections(item);
                        hasVisibleItems = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show message if no results
                const noResultsMsg = document.getElementById('no-results-message');
                if (!hasVisibleItems) {
                    if (!noResultsMsg) {
                        const container = document.getElementById('method-content-container');
                        const msg = document.createElement('div');
                        msg.id = 'no-results-message';
                        msg.className = 'text-center py-8 text-gray-500';
                        msg.textContent = 'No logs found matching your search';
                        container.appendChild(msg);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }

            function resetSearch() {
                document.querySelectorAll('.log-item').forEach(item => {
                    item.style.display = 'block';
                });
                
                const noResultsMsg = document.getElementById('no-results-message');
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
        }

        // Initialize log selection
        function initLogSelection() {
            document.addEventListener('click', function(e) {
                const logItem = e.target.closest('.log-item');
                
                if (logItem) {
                    // Remove active state from all log items
                    document.querySelectorAll('.log-item').forEach(item => {
                        item.classList.remove('border-blue-300', 'bg-blue-50', 'ring-2', 'ring-blue-100');
                    });
                    
                    // Add active state to clicked log item
                    logItem.classList.add('border-blue-300', 'bg-blue-50', 'ring-2', 'ring-blue-100');
                    
                    // Update current log
                    currentLog = JSON.parse(decodeURIComponent(logItem.dataset.logData));

                    // Update request header
                    updateRequestHeader(currentLog);
                    
                    // Update all detail tabs
                    updateAllDetailTabs(currentLog);
                    
                    // Scroll to top of details panel
                    document.getElementById('tab-content-container').scrollTop = 0;
                }
            });
        }


        // Initialize collapsible sections
        function initCollapsible() {
            const list = document.getElementById('method-content-container');
            if (!list || list.dataset.collapsibleBound === '1') return;
            list.dataset.collapsibleBound = '1';
            list.addEventListener('click', function(e) {
                const header = e.target.closest('.endpoint-header');
                if (!header) return;
                if (e.target.closest('.log-item')) return;
                const container = header.closest('.endpoint-group').querySelector('.logs-container');
                const icon = header.querySelector('.toggle-icon');
                const parentMethodSection = header.closest('#method-content-container') || document.getElementById('method-content-container');
                if (parentMethodSection) {
                    parentMethodSection.querySelectorAll('.endpoint-group .logs-container:not(.hidden)').forEach(open => {
                        if (open !== container) {
                            open.classList.add('hidden');
                            const hdr = open.closest('.endpoint-group')?.querySelector('.endpoint-header .toggle-icon');
                            if (hdr) {
                                hdr.classList.remove('ri-arrow-up-s-line');
                                hdr.classList.add('ri-arrow-down-s-line');
                            }
                        }
                    });
                }
                if (container.classList.contains('hidden')) {
                    container.classList.remove('hidden');
                    if (icon) {
                        icon.classList.remove('ri-arrow-down-s-line');
                        icon.classList.add('ri-arrow-up-s-line');
                    }
                } else {
                    container.classList.add('hidden');
                    if (icon) {
                        icon.classList.remove('ri-arrow-up-s-line');
                        icon.classList.add('ri-arrow-down-s-line');
                    }
                }
            });
        }

        // Show parent sections for search results
        function showParentSections(element) {
            const endpointGroup = element.closest('.endpoint-group');
            if (endpointGroup) {
                const logsContainer = endpointGroup.querySelector('.logs-container');
                const endpointHeader = endpointGroup.querySelector('.endpoint-header');
                
                if (logsContainer) {
                    logsContainer.classList.remove('hidden');
                    const endpointIcon = endpointHeader.querySelector('.toggle-icon');
                    endpointIcon.classList.remove('ri-arrow-down-s-line');
                    endpointIcon.classList.add('ri-arrow-up-s-line');
                }
            }
        }

        // Update request header
        function updateRequestHeader(log) {
            const header = document.getElementById('request-header');
            const method = log.method || 'GET';
            const endpoint = extractEndpoint(log.url || '');
            const time = formatTime(log.date || '');
            const status = log.status || 'N/A';
            const responseTime = (log.response_time_ms || log.responseTime || 0).toFixed(2);
            const endpointCount = (groupedLogs[method] && groupedLogs[method][endpoint]) ? groupedLogs[method][endpoint].length : 0;
            const methodClass = getMethodClass(method);

            header.innerHTML = `
                <span class="px-2.5 py-1 rounded ${methodClass} text-xs font-bold">${method}</span>
                <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 font-mono text-xs truncate max-w-[280px]">${endpoint}</span>
                <span class="px-2 py-1 rounded bg-gray-50 text-gray-600 text-xs">${time}</span>
                <span class="px-2 py-1 rounded text-xs font-bold ${getStatusClass(status)}">${status}</span>
                <span class="px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs">${responseTime}ms</span>
            `;
        }

        // Update all detail tabs
        function updateAllDetailTabs(log) {
            updateTabContent('request', log);
            updateTabContent('response', log);
            updateTabContent('headers', log);
            updateTabContent('metadata', log);
            
            // Make sure the active tab is visible
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            document.getElementById(`tab-${activeTab}`).classList.remove('hidden');
        }

        // Update tab content
        function updateTabContent(tabName, log) {
            const tabContainer = document.getElementById(`tab-${tabName}`);
            
            // Update content based on tab
            switch(tabName) {
                case 'request':
                    updateRequestTab(log, tabContainer);
                    break;
                case 'response':
                    updateResponseTab(log, tabContainer);
                    break;
                case 'headers':
                    updateHeadersTab(log, tabContainer);
                    break;
                case 'metadata':
                    updateMetadataTab(log, tabContainer);
                    break;
            }
        }

        // Update request tab
        function updateRequestTab(log, container) {
            const template = document.getElementById('tab-request-template');
            container.innerHTML = template.innerHTML;
            
            // Update method badge
            const method = log.method || 'GET';
            const methodClass = getMethodClass(method);
            const badge = container.querySelector('.method-badge');
            badge.className = `px-3 py-1 rounded text-sm font-bold ${methodClass}`;
            badge.textContent = method;
            
            // Update URL
            container.querySelector('.url-content').textContent = log.url || 'N/A';
            
            // Update body
            const body = log.body || log.request_body || {};
            let bodyContent = 'No request body';
            
            if (Object.keys(body).length > 0) {
                try {
                    bodyContent = JSON.stringify(body, null, 2);
                } catch (e) {
                    bodyContent = typeof body === 'string' ? body : String(body);
                }
            }
            
            container.querySelector('.body-content').textContent = bodyContent;
        }

        // Update response tab
        function updateResponseTab(log, container) {
            const template = document.getElementById('tab-response-template');
            container.innerHTML = template.innerHTML;
            
            // Update status badge
            const status = log.status || 0;
            const statusClass = getStatusClass(status);
            const statusText = getStatusText(status);
            
            const badge = container.querySelector('.status-badge');
            badge.className = `px-3 py-1 rounded text-sm font-bold ${statusClass}`;
            badge.textContent = `${status} ${statusText}`;
            
            // Update response time
            const responseTime = (log.response_time_ms || log.responseTime || 0).toFixed(2);
            container.querySelector('.response-time').textContent = `${responseTime}ms`;
            
            // Update content length
            const contentLength = log.content_length || log.contentLength || 0;
            const size = formatBytes(contentLength);
            container.querySelector('.content-length').textContent = size;
            
            // Update response body
            const response = log.response || log.response_body || {};
            let responseContent = 'No response body';
            
            if (Object.keys(response).length > 0) {
                try {
                    responseContent = JSON.stringify(response, null, 2);
                } catch (e) {
                    responseContent = typeof response === 'string' ? response : String(response);
                }
            }
            
            container.querySelector('.response-content').textContent = responseContent;
        }

        // Update headers tab
        function updateHeadersTab(log, container) {
            const template = document.getElementById('tab-headers-template');
            container.innerHTML = template.innerHTML;
            
            const tbody = container.querySelector('.headers-body');
            const headers = log.headers || log.request_headers || {};
            
            // Combine request and response headers
            const allHeaders = { ...headers };
            if (log.response_headers) {
                Object.assign(allHeaders, log.response_headers);
            }
            
            if (Object.keys(allHeaders).length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                            No headers available
                        </td>
                    </tr>
                `;
            } else {
                for (const [key, value] of Object.entries(allHeaders)) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 font-mono">${key}</td>
                        <td class="px-6 py-3 text-sm text-gray-700 break-all font-mono">${value}</td>
                    `;
                    tbody.appendChild(row);
                }
            }
        }

        // Update metadata tab
        function updateMetadataTab(log, container) {
            const template = document.getElementById('tab-metadata-template');
            container.innerHTML = template.innerHTML;
            
            // Update request info
            container.querySelector('.ip-address').textContent = log.ip || 'Unknown';
            container.querySelector('.user-agent').textContent = log.user_agent || log.userAgent || 'Unknown';
            container.querySelector('.timestamp').textContent = formatTime(log.date || '') || 'N/A';
            
            // Update performance
            const responseTime = (log.response_time_ms || log.responseTime || 0).toFixed(2);
            const contentLength = log.content_length || log.contentLength || 0;
            const status = log.status || 0;
            
            container.querySelector('.response-time').textContent = `${responseTime}ms`;
            container.querySelector('.content-length').textContent = formatBytes(contentLength);
            
            const statusClass = getStatusClass(status);
            const statusBadge = container.querySelector('.status-badge');
            statusBadge.className = `px-2 py-1 rounded text-xs ${statusClass}`;
            statusBadge.textContent = status;
            
            // Update additional details
            container.querySelector('.http-version').textContent = log.http_version || log.httpVersion || 'HTTP/1.1';
            container.querySelector('.content-type').textContent = log.content_type || log.contentType || 'Unknown';
            container.querySelector('.protocol').textContent = log.protocol || 'HTTP';
            container.querySelector('.server-name').textContent = "{{ $server->ip }}";
        }

        // Show empty state
        function showEmptyState() {
            const template = document.getElementById('empty-state-template');
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.innerHTML = template.innerHTML;
                tab.classList.add('hidden');
            });
            
            // Show first tab
            document.getElementById('tab-request').classList.remove('hidden');
            
            // Update header
            document.getElementById('request-header').innerHTML = `
                <span class="text-gray-500">No endpoint selected</span>
            `;
        }

        // Utility functions
        function extractEndpoint(url) {
            if (!url) return '/';
            try {
                const u = new URL(url);
                return u.pathname + u.search;
            } catch {
                // If URL parsing fails, try to extract path
                const match = url.match(/https?:\/\/[^\/]+(\/[^?#]*)/);
                return match ? match[1] : url;
            }
        }

        function formatTime(dateString) {
            if (!dateString) return 'N/A';
            try {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit',
                    hour12: true 
                });
            } catch {
                return dateString;
            }
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function getStatusClass(status) {
            if (status >= 500) return 'bg-red-100 text-red-800';
            if (status >= 400) return 'bg-yellow-100 text-yellow-800';
            if (status >= 300) return 'bg-blue-100 text-blue-800';
            if (status >= 200) return 'bg-green-100 text-green-800';
            return 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status) {
            const statusMap = {
                200: 'OK',
                201: 'Created',
                204: 'No Content',
                400: 'Bad Request',
                401: 'Unauthorized',
                403: 'Forbidden',
                404: 'Not Found',
                500: 'Internal Server Error',
                502: 'Bad Gateway',
                503: 'Service Unavailable'
            };
            return statusMap[status] || '';
        }

        function getMethodClass(method) {
            const methodUpper = method.toUpperCase();
            if (methodUpper === 'GET') return 'bg-blue-100 text-blue-800';
            if (methodUpper === 'POST') return 'bg-green-100 text-green-800';
            if (methodUpper === 'PUT') return 'bg-yellow-100 text-yellow-800';
            if (methodUpper === 'DELETE') return 'bg-red-100 text-red-800';
            if (methodUpper === 'PATCH') return 'bg-purple-100 text-purple-800';
            return 'bg-gray-100 text-gray-800';
        }

        function showError(message) {
            const errorBanner = document.getElementById('error-banner');
            errorBanner.textContent = message;
            errorBanner.classList.remove('hidden');
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                hideError();
            }, 5000);
        }

        function hideError() {
            document.getElementById('error-banner').classList.add('hidden');
        }
        
        function showApiStatus(type, message) {
            const dotWrap = document.getElementById('apiStatusDot');
            if (dotWrap) {
                const dot = dotWrap.querySelector('span') || dotWrap;
                dotWrap.classList.remove('hidden');
                dot.className = `inline-block w-2 h-2 rounded-full ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Custom styles for better visual appearance */
    .method-header:hover .toggle-icon,
    .endpoint-header:hover .toggle-icon {
        color: #4b5563;
    }

    .log-item {
        transition: all 0.2s ease;
    }

    .log-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .method-tab-btn, .detail-tab-btn {
        transition: all 0.2s ease;
    }

    .method-tab-btn:hover {
        background-color: #f9fafb;
    }

    .detail-tab-btn:hover {
        background-color: #f9fafb;
    }

    /* Scrollbar styling */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Animation for refresh button */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 0.5s linear infinite;
    }

    /* Smooth transitions */
    .method-header,
    .endpoint-header,
    .log-item,
    .method-tab-btn,
    .detail-tab-btn {
        transition: all 0.2s ease-in-out;
    }

    /* Preformatted text styling */
    pre {
        font-family: 'Fira Code', 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.5;
    }

    /* Truncation for long URLs */
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .break-all {
        word-break: break-all;
    }

    /* Method tabs styling */
    #method-tabs-container {
        overflow-x: auto;
        white-space: nowrap;
    }

    .method-tab-btn {
        display: inline-flex;
        align-items: center;
        font-size: 0.875rem;
        border-bottom: 2px solid transparent;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .grid-cols-1 {
            grid-template-columns: 1fr;
        }
        
        .lg\:col-span-1,
        .lg\:col-span-2 {
            grid-column: span 1;
        }
        
        .method-tab-btn, .detail-tab-btn {
            padding: 0.75rem 0.5rem;
            font-size: 0.75rem;
        }
        
        #method-tabs-container {
            padding-bottom: 0.25rem;
        }
    }
</style>
@endpush
