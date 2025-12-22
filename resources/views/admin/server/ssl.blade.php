@extends('layouts.admin')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-hidden">
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top bar -->
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10">
            <div class="flex items-center gap-4">
                <h1 class="text-lg font-semibold text-gray-800">Server Assets</h1>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600 font-medium">{{ $server->ip }}</span>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">SSL</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-500">Last updated: {{ now()->format('M d, H:i') }}</div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="flex flex-col lg:flex-row gap-6">
                
                <!-- Left Column: Monitored Domains -->
                <div class="w-full lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 h-full flex flex-col">
                        <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <h2 class="font-semibold text-gray-800">Monitored Domains</h2>
                        </div>
                        
                        <div class="p-3 space-y-2 overflow-y-auto flex-1">
                            <!-- List Item 1 -->
                            <div class="p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-100 group transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-purple-500"></div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">heylivo.com</div>
                                            <div class="text-xs text-gray-400 group-hover:text-gray-500">Click to check SSL</div>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- List Item 2 (Active) -->
                            <div class="p-3 rounded-lg bg-purple-600 shadow-md cursor-pointer border border-purple-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-white"></div>
                                        <div>
                                            <div class="text-sm font-semibold text-white">google.com</div>
                                            <div class="text-xs text-purple-200">Click to check SSL</div>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- List Item 3 -->
                            <div class="p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-100 group transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-purple-500"></div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">facebook.com</div>
                                            <div class="text-xs text-gray-400 group-hover:text-gray-500">Click to check SSL</div>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- List Item 4 -->
                            <div class="p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-100 group transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-purple-500"></div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">apprise.it</div>
                                            <div class="text-xs text-gray-400 group-hover:text-gray-500">Click to check SSL</div>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: SSL Details -->
                <div class="flex-1 min-w-0 h-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">google.com</h1>
                                    <p class="text-sm text-gray-500">SSL Certificate Information</p>
                                </div>
                            </div>
                            <button class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                Copy All
                            </button>
                        </div>

                        <!-- Status Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <!-- Status -->
                            <div class="bg-orange-50 rounded-lg p-5 border border-orange-100">
                                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">STATUS</div>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-orange-500"></div>
                                    <div class="text-xl font-bold text-orange-700">Renew Soon</div>
                                </div>
                            </div>
                            <!-- Days Remaining -->
                            <div class="bg-blue-50 rounded-lg p-5 border border-blue-100">
                                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">DAYS REMAINING</div>
                                <div class="text-xl font-bold text-blue-600">65 days</div>
                            </div>
                            <!-- Expires On -->
                            <div class="bg-purple-50 rounded-lg p-5 border border-purple-100">
                                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">EXPIRES ON</div>
                                <div class="text-xl font-bold text-purple-600">Feb 25</div>
                            </div>
                        </div>

                        <!-- Certificate Details -->
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800">Certificate Details</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Issuer -->
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase">ISSUER</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 font-mono">
                                        C=US, O=Google Trust Services, CN=WR2
                                    </div>
                                </div>

                                <!-- Subject -->
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase">SUBJECT</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 font-mono">
                                        CN=*.google.com
                                    </div>
                                </div>

                                <!-- Valid From -->
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase">VALID FROM</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800">
                                        December 3, 2025
                                    </div>
                                </div>

                                <!-- Valid Until -->
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase">VALID UNTIL</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800">
                                        February 25, 2026
                                    </div>
                                </div>

                                <!-- Serial Number -->
                                <div class="space-y-1 md:col-span-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">SERIAL NUMBER</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 font-mono">
                                        56C74F44A4CE9B190AD981A4FC39C286
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
