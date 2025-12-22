@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <!-- Top Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Server Status -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <h2 class="text-gray-700 font-medium mb-4">Server: {{ $server->ip }}</h2>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-gray-500">Status:</span>
                    <span class="text-green-600 font-medium">Running</span>
                    <span class="h-3 w-3 rounded-full bg-green-500"></span>
                </div>
                
                <div class="mb-2">
                    <span class="text-purple-600 text-3xl font-semibold">CPU: --%</span>
                </div>
                <div class="text-gray-500 mb-6">
                    Memory: -- MB used
                </div>
            </div>
            <div>
                <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                    View Details
                </button>
            </div>
        </div>

        <!-- Server Metrics -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h2 class="text-gray-700 font-medium mb-2">Server Metrics</h2>
            <div class="text-xs text-gray-400 mb-6">Last Updated: --</div>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- CPU -->
                <div class="flex flex-col gap-1">
                    <div class="h-10 w-10 rounded bg-purple-100 flex items-center justify-center text-purple-600 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500">CPU</span>
                    <span class="text-sm font-medium">-- %</span>
                </div>

                <!-- Memory -->
                <div class="flex flex-col gap-1">
                    <div class="h-10 w-10 rounded bg-green-100 flex items-center justify-center text-green-600 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500">Memory Used</span>
                    <span class="text-sm font-medium">-- MB</span>
                </div>

                <!-- Disk -->
                <div class="flex flex-col gap-1">
                    <div class="h-10 w-10 rounded bg-yellow-100 flex items-center justify-center text-yellow-600 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500">Disk Used</span>
                    <span class="text-sm font-medium">-- %</span>
                </div>

                <!-- Load Avg -->
                <div class="flex flex-col gap-1">
                    <div class="h-10 w-10 rounded bg-blue-100 flex items-center justify-center text-blue-500 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500">Load Avg</span>
                    <span class="text-sm font-medium">--</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- CPU Usage -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">CPU Usage</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
            <div class="py-4">
                <span class="text-2xl font-semibold text-gray-800">--%</span>
                <span class="text-sm text-gray-500 ml-2">Latest CPU Usage</span>
            </div>
        </div>

        <!-- RAM Usage -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">RAM Usage</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
            <div class="py-4">
                <span class="text-2xl font-semibold text-gray-800">--%</span>
                <span class="text-sm text-gray-500 ml-2">Latest RAM Usage</span>
            </div>
        </div>

        <!-- Disk Usage -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 relative">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">Disk Usage</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
            <div class="py-4">
                <span class="text-2xl font-semibold text-gray-800">--%</span>
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
                        <!-- Placeholder rows -->
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">-</td>
                            <td class="px-3 py-2">-</td>
                            <td class="px-3 py-2">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Logs -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 lg:col-span-1">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-gray-700 font-medium">Logs</h3>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>
            </div>
            <div class="h-32 flex items-center justify-center text-gray-400">
                <!-- Log content would go here -->
            </div>
        </div>

        <!-- Traffic -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Input Traffic -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-gray-700 font-medium mb-4">Input Traffic</h3>
                <div class="text-center py-4">
                    <div class="text-gray-500 mb-1">Input MB</div>
                    <div class="text-xl font-semibold">--</div>
                </div>
            </div>

            <!-- Output Traffic -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-gray-700 font-medium mb-4">Output Traffic</h3>
                <div class="text-center py-4">
                    <div class="text-gray-500 mb-1">Output MB</div>
                    <div class="text-xl font-semibold">--</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
