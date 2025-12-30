@extends('layouts.admin')

@section('content')
<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10 sticky top-0 mb-6 shadow-sm">
    <div class="flex items-center gap-4">
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            SSL Certificate Monitor
        </h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
            <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-sm text-gray-600" id="apiStatus">API: Online</span>
        </div>
        <div class="text-sm text-gray-500" id="lastUpdated">Last updated: {{ now()->format('M d, H:i') }}</div>
    </div>
</header>

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Left Column: Monitored Domains -->
    <div class="w-full lg:w-80">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 h-full flex flex-col">
            <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-white">
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Monitored Domains
                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded-full ml-2" id="domainCount">0</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Click any domain to check SSL certificate</p>
            </div>
            
            <!-- Search and Filter -->
            <div class="p-4 border-b border-gray-100">
                <div class="relative">
                    <input type="text" 
                           id="domainSearch" 
                           placeholder="Search domains..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Domains List -->
            <div id="sslTabs" class="p-3 space-y-2 overflow-y-auto flex-1">
                @if(isset($hostnames) && $hostnames->count() > 0)
                    @foreach($hostnames as $index => $hostname)
                        <div class="ssl-domain-item p-3 rounded-lg cursor-pointer border border-gray-200 hover:border-purple-300 hover:bg-purple-50 group transition-all duration-200 animate-fadeIn" 
                             data-ssl-key="{{ Str::slug($hostname->hostname) }}" 
                             data-hostname="{{ $hostname->hostname }}"
                             style="animation-delay: {{ $index * 50 }}ms">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="h-3 w-3 rounded-full bg-purple-500 animate-pulse"></div>
                                        <div class="h-3 w-3 rounded-full bg-purple-200 absolute top-0 left-0 animate-ping opacity-75"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-800 truncate max-w-[180px]">{{ $hostname->hostname }}</div>
                                        <div class="text-xs text-gray-400 group-hover:text-gray-600">Click to check SSL</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-gray-300 status-indicator" data-hostname="{{ $hostname->hostname }}"></div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover:text-purple-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Default sample domains -->
                    @foreach(['google.com', 'github.com', 'facebook.com', 'twitter.com', 'cloudflare.com'] as $index => $domain)
                        <div class="ssl-domain-item p-3 rounded-lg cursor-pointer border border-gray-200 hover:border-purple-300 hover:bg-purple-50 group transition-all duration-200 animate-fadeIn" 
                             data-ssl-key="{{ Str::slug($domain) }}" 
                             data-hostname="{{ $domain }}"
                             style="animation-delay: {{ $index * 50 }}ms">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="h-3 w-3 rounded-full bg-purple-500 animate-pulse"></div>
                                        <div class="h-3 w-3 rounded-full bg-purple-200 absolute top-0 left-0 animate-ping opacity-75"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-800 truncate max-w-[180px]">{{ $domain }}</div>
                                        <div class="text-xs text-gray-400 group-hover:text-gray-600">Click to check SSL</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-gray-300 status-indicator" data-hostname="{{ $domain }}"></div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover:text-purple-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <!-- Loading State -->
            <div id="loadingDomains" class="hidden p-8 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-3"></div>
                <p class="text-sm text-gray-500">Loading domains...</p>
            </div>
            
            <!-- Empty State -->
            <div id="emptyDomains" class="hidden p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 text-sm">No domains found</p>
                <button class="mt-3 px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                    Add Domain
                </button>
            </div>
        </div>
    </div>

    <!-- Right Column: SSL Details -->
    <div class="flex-1 min-w-0 h-full">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 h-full">
            <!-- Empty State -->
            <div id="sslEmpty" class="py-24 text-center">
                <div class="max-w-md mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-300 mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Domain Selected</h3>
                    <p class="text-gray-500 mb-6">Select a domain from the list to view SSL certificate information</p>
                    <button id="refreshDomainsBtn" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors flex items-center gap-2 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh Domains
                    </button>
                </div>
            </div>

            <!-- SSL Details Container -->
            <div id="sslDetailsContainer" class="hidden p-6">
                <!-- Loading State -->
                <div id="sslLoading" class="hidden py-16 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-3 border-purple-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 font-medium">Checking SSL certificate...</p>
                    <p class="text-sm text-gray-400 mt-2" id="checkingDomain"></p>
                </div>

                <!-- Error State -->
                <div id="sslError" class="hidden py-16 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-red-600 mb-2" id="errorTitle">Failed to fetch SSL certificate</h3>
                    <p class="text-gray-600 mb-4" id="errorMessage">The SSL certificate could not be retrieved</p>
                    <button id="retryBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Retry
                    </button>
                </div>

                <!-- SSL Information -->
                <div id="sslInfo" class="hidden">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-purple-50 to-blue-50 flex items-center justify-center text-purple-600 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 id="sslDomainTitle" class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                                    <span id="domainName">google.com</span>
                                    <span id="sslStatusBadge" class="text-xs font-semibold px-2 py-1 rounded-full"></span>
                                </h1>
                                <p class="text-sm text-gray-500">SSL Certificate Information • Updated <span id="lastChecked">just now</span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="refreshSSLBtn" class="flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh
                            </button>
                            <button id="copyAllBtn" class="flex items-center gap-2 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copy All
                            </button>
                        </div>
                    </div>

                    <!-- Status Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="rounded-xl p-5 border bg-gradient-to-br from-white to-gray-50 shadow-sm">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">STATUS</div>
                            <div class="flex items-center gap-2">
                                <div id="statusIndicator" class="h-3 w-3 rounded-full"></div>
                                <div id="statusText" class="text-base font-semibold"></div>
                            </div>
                        </div>
                        <div class="rounded-xl p-5 border bg-gradient-to-br from-white to-blue-50 shadow-sm">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">DAYS REMAINING</div>
                            <div id="daysRemaining" class="text-2xl font-bold text-blue-600"></div>
                        </div>
                        <div class="rounded-xl p-5 border bg-gradient-to-br from-white to-purple-50 shadow-sm">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">EXPIRES ON</div>
                            <div id="expiryDate" class="text-2xl font-bold text-purple-600"></div>
                        </div>
                    </div>

                    <!-- Certificate Details -->
                    <div class="rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 flex items-center gap-2 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm font-semibold text-gray-800">Certificate Details</div>
                        </div>
                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Issuer</div>
                                <div id="sslIssuer" class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 font-mono truncate hover:text-clip"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</div>
                                <div id="sslSubject" class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 font-mono truncate hover:text-clip"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Valid From</div>
                                <div id="sslValidFrom" class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</div>
                                <div id="sslValidUntil" class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800"></div>
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</div>
                                <div id="sslSerial" class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 font-mono truncate hover:text-clip"></div>
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Alternative Names (SAN)</div>
                                <div id="sslSan" class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 font-mono max-h-20 overflow-y-auto"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-6">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Certificate Lifetime</span>
                            <span id="certProgressText">0%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div id="certProgressBar" class="h-full bg-gradient-to-r from-green-500 to-yellow-500 transition-all duration-500" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span id="startDate">Valid from</span>
                            <span id="endDate">Valid until</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- API Test Modal -->
<div id="apiTestModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            API Connection Test
        </h3>
        <div class="space-y-4">
            <div id="apiTestStatus" class="flex items-center gap-3">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-purple-600"></div>
                <span class="text-gray-600">Testing API connection...</span>
            </div>
            <div id="apiTestResult" class="hidden text-sm"></div>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button id="closeApiTest" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">
                    Close
                </button>
                <button id="retryApiTest" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                    Retry Test
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes progress {
        0% { width: 0%; }
        100% { width: var(--progress); }
    }
    .progress-animate {
        animation: progress 1s ease-out forwards;
    }
</style>
@endpush

@push('footer_scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Configuration
        const API_BASE_URL = 'http://157.245.207.91:3001';
        const SSL_CHECK_ENDPOINT = '/api/ssl-info';
        
        // State
        let currentHostname = null;
        let sslDataCache = {};
        let isChecking = false;
        
        // DOM Elements
        const domainSearch = document.getElementById('domainSearch');
        const sslEmpty = document.getElementById('sslEmpty');
        const sslDetailsContainer = document.getElementById('sslDetailsContainer');
        const sslLoading = document.getElementById('sslLoading');
        const sslError = document.getElementById('sslError');
        const sslInfo = document.getElementById('sslInfo');
        const apiStatus = document.getElementById('apiStatus');
        const lastUpdated = document.getElementById('lastUpdated');
        const domainCount = document.getElementById('domainCount');
        const checkingDomain = document.getElementById('checkingDomain');
        const refreshDomainsBtn = document.getElementById('refreshDomainsBtn');
        const refreshSSLBtn = document.getElementById('refreshSSLBtn');
        const copyAllBtn = document.getElementById('copyAllBtn');
        const retryBtn = document.getElementById('retryBtn');
        const sslTabs = document.getElementById('sslTabs');
        const apiTestModal = document.getElementById('apiTestModal');
        const apiTestStatus = document.getElementById('apiTestStatus');
        const apiTestResult = document.getElementById('apiTestResult');
        const closeApiTest = document.getElementById('closeApiTest');
        const retryApiTest = document.getElementById('retryApiTest');
        
        // Initialize
        updateDomainCount();
        checkApiStatus();
        
        // Event Listeners
        domainSearch.addEventListener('input', filterDomains);
        refreshDomainsBtn.addEventListener('click', refreshDomains);
        refreshSSLBtn.addEventListener('click', () => {
            if (currentHostname) {
                fetchSSLInfo(currentHostname, true);
            }
        });
        copyAllBtn.addEventListener('click', copyAllDetails);
        retryBtn.addEventListener('click', () => {
            if (currentHostname) {
                fetchSSLInfo(currentHostname, true);
            }
        });
        closeApiTest.addEventListener('click', () => {
            apiTestModal.classList.add('hidden');
        });
        retryApiTest.addEventListener('click', testApiConnection);
        
        // Domain click handling
        const domainItems = Array.from(document.querySelectorAll('.ssl-domain-item'));
        domainItems.forEach(item => {
            item.addEventListener('click', () => {
                const hostname = item.getAttribute('data-hostname');
                if (hostname && !isChecking) {
                    selectDomain(item, hostname);
                }
            });
        });
        
        // Search functionality
        function filterDomains() {
            const searchTerm = domainSearch.value.toLowerCase();
            const items = document.querySelectorAll('.ssl-domain-item');
            
            items.forEach(item => {
                const domain = item.getAttribute('data-hostname').toLowerCase();
                if (domain.includes(searchTerm)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
        
        // Domain selection
        function selectDomain(item, hostname) {
            // Update UI
            document.querySelectorAll('.ssl-domain-item').forEach(el => {
                el.classList.remove('bg-purple-50', 'border-purple-300', 'text-purple-700');
                el.classList.add('border-gray-200', 'hover:border-purple-300', 'hover:bg-purple-50');
            });
            
            item.classList.add('bg-purple-50', 'border-purple-300');
            item.classList.remove('hover:border-purple-300', 'hover:bg-purple-50');
            
            // Show SSL details
            sslEmpty.classList.add('hidden');
            sslDetailsContainer.classList.remove('hidden');
            sslInfo.classList.add('hidden');
            sslError.classList.add('hidden');
            sslLoading.classList.remove('hidden');
            checkingDomain.textContent = hostname;
            
            currentHostname = hostname;
            fetchSSLInfo(hostname);
        }
        
        // Fetch SSL information
        async function fetchSSLInfo(hostname, forceRefresh = false) {
            if (isChecking) return;
            
            isChecking = true;
            sslLoading.classList.remove('hidden');
            sslInfo.classList.add('hidden');
            sslError.classList.add('hidden');
            
            // Check cache first
            const cacheKey = hostname.toLowerCase();
            const cacheTime = sslDataCache[cacheKey]?.timestamp;
            const now = Date.now();
            
            if (!forceRefresh && sslDataCache[cacheKey] && (now - cacheTime < 300000)) { // 5 minutes cache
                isChecking = false;
                sslLoading.classList.add('hidden');
                displaySSLInfo(sslDataCache[cacheKey].data);
                return;
            }
            
            try {
                const response = await fetch(`${API_BASE_URL}${SSL_CHECK_ENDPOINT}?host=${encodeURIComponent(hostname)}`);
                
                if (!response.ok) {
                    throw new Error(`API error: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Cache the result
                sslDataCache[cacheKey] = {
                    data: data,
                    timestamp: Date.now()
                };
                
                // Update status indicator
                updateDomainStatus(hostname, data);
                
                // Display data
                sslLoading.classList.add('hidden');
                displaySSLInfo(data);
                
                // Update last checked time
                updateLastChecked();
                
            } catch (error) {
                console.error('SSL fetch error:', error);
                sslLoading.classList.add('hidden');
                sslError.classList.remove('hidden');
                document.getElementById('errorTitle').textContent = 'Connection Failed';
                document.getElementById('errorMessage').textContent = `Could not fetch SSL certificate for ${hostname}. Please check the domain and try again.`;
            } finally {
                isChecking = false;
            }
        }
        
        // Display SSL information
        function displaySSLInfo(data) {
            if (!data) return;
            
            // Update basic info
            document.getElementById('domainName').textContent = currentHostname;
            
            // Calculate days remaining
            const expiryDate = new Date(data.notAfter);
            const now = new Date();
            const daysRemaining = Math.max(0, Math.floor((expiryDate - now) / (1000 * 60 * 60 * 24)));
            
            // Determine status
            let statusConfig = {
                color: 'bg-green-500',
                textColor: 'text-green-700',
                status: 'Valid',
                badgeColor: 'bg-green-100 text-green-800'
            };
            
            if (daysRemaining <= 0) {
                statusConfig = {
                    color: 'bg-red-500',
                    textColor: 'text-red-700',
                    status: 'Expired',
                    badgeColor: 'bg-red-100 text-red-800'
                };
            } else if (daysRemaining <= 7) {
                statusConfig = {
                    color: 'bg-red-500',
                    textColor: 'text-red-700',
                    status: 'Critical',
                    badgeColor: 'bg-red-100 text-red-800'
                };
            } else if (daysRemaining <= 30) {
                statusConfig = {
                    color: 'bg-yellow-500',
                    textColor: 'text-yellow-700',
                    status: 'Warning',
                    badgeColor: 'bg-yellow-100 text-yellow-800'
                };
            } else if (daysRemaining <= 90) {
                statusConfig = {
                    color: 'bg-blue-500',
                    textColor: 'text-blue-700',
                    status: 'Renew Soon',
                    badgeColor: 'bg-blue-100 text-blue-800'
                };
            }
            
            // Update status elements
            document.getElementById('statusIndicator').className = `h-3 w-3 rounded-full ${statusConfig.color} animate-pulse`;
            document.getElementById('statusText').className = `text-base font-semibold ${statusConfig.textColor}`;
            document.getElementById('statusText').textContent = statusConfig.status;
            document.getElementById('sslStatusBadge').className = `text-xs font-semibold px-2 py-1 rounded-full ${statusConfig.badgeColor}`;
            document.getElementById('sslStatusBadge').textContent = statusConfig.status;
            
            // Update numeric values
            document.getElementById('daysRemaining').textContent = `${daysRemaining} days`;
            document.getElementById('expiryDate').textContent = formatDate(expiryDate);
            
            // Update certificate details
            document.getElementById('sslIssuer').textContent = data.issuer || 'Unknown';
            document.getElementById('sslSubject').textContent = data.subject || currentHostname;
            document.getElementById('sslValidFrom').textContent = formatDate(new Date(data.notBefore));
            document.getElementById('sslValidUntil').textContent = formatDate(new Date(data.notAfter));
            document.getElementById('sslSerial').textContent = data.serial || 'N/A';
            document.getElementById('sslSan').textContent = data.san || 'No alternative names';
            
            // Update progress bar
            const startDate = new Date(data.notBefore);
            const endDate = new Date(data.notAfter);
            const totalDuration = endDate - startDate;
            const elapsed = now - startDate;
            const progress = Math.min(100, Math.max(0, (elapsed / totalDuration) * 100));
            
            const progressBar = document.getElementById('certProgressBar');
            progressBar.style.setProperty('--progress', `${progress}%`);
            progressBar.classList.add('progress-animate');
            
            document.getElementById('certProgressText').textContent = `${progress.toFixed(1)}%`;
            document.getElementById('startDate').textContent = `Valid from ${formatDate(startDate, true)}`;
            document.getElementById('endDate').textContent = `Valid until ${formatDate(endDate, true)}`;
            
            // Show the info section
            sslInfo.classList.remove('hidden');
        }
        
        // Update domain status indicator
        function updateDomainStatus(hostname, data) {
            const indicator = document.querySelector(`.status-indicator[data-hostname="${hostname}"]`);
            if (!indicator || !data) return;
            
            const expiryDate = new Date(data.notAfter);
            const now = new Date();
            const daysRemaining = Math.floor((expiryDate - now) / (1000 * 60 * 60 * 24));
            
            let color = 'bg-gray-300';
            if (daysRemaining <= 0) {
                color = 'bg-red-500';
            } else if (daysRemaining <= 7) {
                color = 'bg-red-500 animate-pulse';
            } else if (daysRemaining <= 30) {
                color = 'bg-yellow-500';
            } else if (daysRemaining <= 90) {
                color = 'bg-blue-500';
            } else {
                color = 'bg-green-500';
            }
            
            indicator.className = `w-2 h-2 rounded-full ${color}`;
        }
        
        // Copy all details to clipboard
        function copyAllDetails() {
            const domain = document.getElementById('domainName').textContent;
            const status = document.getElementById('statusText').textContent;
            const daysRemaining = document.getElementById('daysRemaining').textContent;
            const expiryDate = document.getElementById('expiryDate').textContent;
            const issuer = document.getElementById('sslIssuer').textContent;
            const subject = document.getElementById('sslSubject').textContent;
            const validFrom = document.getElementById('sslValidFrom').textContent;
            const validUntil = document.getElementById('sslValidUntil').textContent;
            const serial = document.getElementById('sslSerial').textContent;
            const san = document.getElementById('sslSan').textContent;
            
            const text = `SSL Certificate Details - ${domain}
===============================
Status: ${status}
Days Remaining: ${daysRemaining}
Expires: ${expiryDate}
Issuer: ${issuer}
Subject: ${subject}
Valid From: ${validFrom}
Valid Until: ${validUntil}
Serial Number: ${serial}
Subject Alternative Names: ${san}
===============================
Last Checked: ${new Date().toLocaleString()}`;
            
            navigator.clipboard.writeText(text).then(() => {
                const originalText = copyAllBtn.innerHTML;
                copyAllBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Copied!
                `;
                copyAllBtn.classList.add('bg-green-600');
                
                setTimeout(() => {
                    copyAllBtn.innerHTML = originalText;
                    copyAllBtn.classList.remove('bg-green-600');
                }, 2000);
            });
        }
        
        // Check API status
        async function checkApiStatus() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/health`);
                if (response.ok) {
                    apiStatus.innerHTML = `
                        <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span>API: Online</span>
                    `;
                } else {
                    throw new Error('API not responding');
                }
            } catch (error) {
                apiStatus.innerHTML = `
                    <div class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></div>
                    <span>API: Offline</span>
                `;
            }
        }
        
        // Test API connection
        async function testApiConnection() {
            apiTestModal.classList.remove('hidden');
            apiTestStatus.classList.remove('hidden');
            apiTestResult.classList.add('hidden');
            apiTestResult.innerHTML = '';
            
            try {
                const response = await fetch(`${API_BASE_URL}/api/health`, {
                    signal: AbortSignal.timeout(5000)
                });
                
                const data = await response.json();
                
                apiTestStatus.classList.add('hidden');
                apiTestResult.classList.remove('hidden');
                apiTestResult.innerHTML = `
                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-2 text-green-700 font-medium mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            API Connection Successful
                        </div>
                        <div class="text-sm text-green-600">
                            <p>Status: ${response.status} ${response.statusText}</p>
                            <p class="mt-1">Response time: ${data.responseTime || 'N/A'}</p>
                        </div>
                    </div>
                `;
                
                apiStatus.innerHTML = `
                    <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span>API: Online</span>
                `;
                
            } catch (error) {
                apiTestStatus.classList.add('hidden');
                apiTestResult.classList.remove('hidden');
                apiTestResult.innerHTML = `
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center gap-2 text-red-700 font-medium mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            API Connection Failed
                        </div>
                        <div class="text-sm text-red-600">
                            <p>Error: ${error.message}</p>
                            <p class="mt-1">Please check the API server configuration.</p>
                        </div>
                    </div>
                `;
                
                apiStatus.innerHTML = `
                    <div class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></div>
                    <span>API: Offline</span>
                `;
            }
        }
        
        // Refresh domains
        function refreshDomains() {
            const items = document.querySelectorAll('.ssl-domain-item');
            items.forEach((item, index) => {
                item.style.animationDelay = `${index * 50}ms`;
                item.classList.add('animate-fadeIn');
            });
            
            updateLastUpdated();
        }
        
        // Helper functions
        function formatDate(date, short = false) {
            if (short) {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
            return date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }
        
        function updateDomainCount() {
            const count = document.querySelectorAll('.ssl-domain-item').length;
            domainCount.textContent = count;
        }
        
        function updateLastUpdated() {
            const now = new Date();
            lastUpdated.textContent = `Last updated: ${now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} ${now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
        }
        
        function updateLastChecked() {
            const now = new Date();
            document.getElementById('lastChecked').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
        
        // Auto-refresh every 5 minutes
        setInterval(() => {
            if (currentHostname) {
                fetchSSLInfo(currentHostname, true);
            }
            updateLastUpdated();
            checkApiStatus();
        }, 300000); // 5 minutes
        
        // Test API connection on double-click status
        apiStatus.addEventListener('dblclick', testApiConnection);
        
        // Show API test modal on Ctrl+Shift+A
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key === 'A') {
                e.preventDefault();
                testApiConnection();
            }
        });
    });
</script>
@endpush