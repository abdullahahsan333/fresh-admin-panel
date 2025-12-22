@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <!-- Top Row: 4 Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Connected Clients -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded bg-purple-100 flex items-center justify-center text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-xl font-semibold text-gray-800">--</span>
            </div>
            <div class="text-sm font-medium text-gray-700">Connected Clients</div>
            <div class="text-xs text-gray-500">Active Redis connections</div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-purple-500"></div>
        </div>

        <!-- Ops / Sec -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded bg-yellow-100 flex items-center justify-center text-yellow-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-xl font-semibold text-gray-800">--</span>
            </div>
            <div class="text-sm font-medium text-gray-700">Ops / Sec</div>
            <div class="text-xs text-gray-500">Instantaneous operations</div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-yellow-400"></div>
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
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-red-500"></div>
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
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-400"></div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Operations Per Second -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">Operations Per Second</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
            <div class="py-8">
                <div class="text-lg font-medium text-gray-700 mb-1">-- Current Ops/Sec</div>
                <div class="h-24 w-full bg-gray-50 rounded flex items-end">
                    <!-- Placeholder for line chart -->
                    <div class="w-full border-t border-gray-200"></div>
                </div>
            </div>
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
                <div class="mb-4 text-center">
                    <span class="text-lg font-medium text-gray-700">0%</span>
                    <span class="text-sm text-gray-500 block">Memory Usage</span>
                </div>
                <div class="w-full flex justify-between px-4 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-purple-200"></span>
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
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">Commands vs Memory</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
            <div class="h-32 flex items-center justify-center text-gray-400 text-sm bg-gray-50 rounded">
                Chart Placeholder
            </div>
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
                <span class="px-3 py-1 text-xs font-bold text-white bg-purple-500 rounded-sm">GET</span>
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
                <div class="mb-4 text-center">
                    <span class="text-lg font-medium text-gray-700">0%</span>
                    <span class="text-sm text-gray-500 block">Hit Ratio</span>
                </div>
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
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">Network Traffic</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
            
            <div class="flex-1 flex items-center justify-center text-gray-400 text-sm mb-4">
                Input/Output Traffic
            </div>

            <div class="flex justify-between items-end mt-auto">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-8 h-8 rounded bg-purple-100"></span>
                        <div class="text-xs text-gray-500">Input Kb/s</div>
                    </div>
                    <div class="text-lg font-medium text-gray-800">0</div>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-8 h-8 rounded bg-blue-100"></span>
                        <div class="text-xs text-gray-500">Output Kb/s</div>
                    </div>
                    <div class="text-lg font-medium text-gray-800">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fourth Row: Bottom Left Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <!-- Connected Slaves -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-gray-700 font-medium mb-4">Connected Slaves</h3>
                <div class="text-right">
                    <span class="text-lg font-semibold text-gray-800">0</span>
                    <span class="text-sm text-gray-500 ml-1">Slaves</span>
                </div>
            </div>
            
            <!-- Blocked Clients -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-gray-700 font-medium mb-4">Blocked Clients</h3>
                <div class="text-right">
                    <span class="text-lg font-semibold text-gray-800">0</span>
                    <span class="text-sm text-gray-500 ml-1">Blocked</span>
                </div>
            </div>
        </div>
        
        <!-- Empty space on the right (as per mockup layout having empty space) or maybe content? 
             The mockup shows only left column filled in bottom row, or maybe it wraps. 
             I'll leave the other 2 columns empty or omitted if not needed.
        -->
    </div>
</div>
@endsection