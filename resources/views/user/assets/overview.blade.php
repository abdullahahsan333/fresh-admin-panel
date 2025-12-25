@extends('layouts.user')
@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Assets Overview</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: {{ now()->format('M d, H:i') }}</div>
    </div>
    {{-- <div class="flex items-center gap-4">
        <a href="{{ route('user.assets.index') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Assets & Services</a>
    </div> --}}
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800 pl-2">Server List</h2>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-2">IP</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Services</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($server ?? collect()) as $srv)
                    <tr class="border-b border-gray-100">
                        <td class="px-3 py-2 font-medium text-gray-800">{{ $srv->ip }}</td>
                        <td class="px-3 py-2"><span class="inline-flex items-center gap-1 text-emerald-600"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Active</span></td>
                        <td class="px-3 py-2">
                            <a class="text-[rgb(var(--color-primary))] hover:underline" href="{{ route('user.server.linux', $srv->id) }}">Linux</a>,
                            <a class="text-[rgb(var(--color-primary))] hover:underline" href="{{ route('user.server.mysql', $srv->id) }}">MySQL</a>,
                            <a class="text-[rgb(var(--color-primary))] hover:underline" href="{{ route('user.server.mongodb', $srv->id) }}">MongoDB</a>,
                            <a class="text-[rgb(var(--color-primary))] hover:underline" href="{{ route('user.server.redis', $srv->id) }}">Redis</a>,
                            <a class="text-[rgb(var(--color-primary))] hover:underline" href="{{ route('user.server.api_log', $srv->id) }}">API Log</a>,
                            <a class="text-[rgb(var(--color-primary))] hover:underline" href="{{ route('user.server.scheduler', $srv->id) }}">Scheduler</a>,
                            <a class="text-[rgb(var(--color-primary))] hover:underline" href="{{ route('user.server.ssl', $srv->id) }}">SSL</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="px-3 py-4 text-sm text-gray-500" colspan="3">No servers yet. Add an IP in Assets & Services.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
 
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <h3 class="font-semibold text-lg text-gray-800">MySQL</h3>
                <p class="text-sm text-gray-400 mt-1">--</p>
            </div>
            <div class="text-right">
                <span class="text-gray-400 text-sm">--</span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <h3 class="font-semibold text-lg text-gray-800">Redis</h3>
                <p class="text-xs text-gray-400 mt-1">2025-08-03 12:00:00</p>
            </div>
            <div class="mt-2">
                <span class="text-green-600 font-bold text-sm">Online</span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <h3 class="font-semibold text-lg text-gray-800">MongoDB</h3>
                <p class="text-xs text-gray-400 mt-1">2025-08-03 12:00:00</p>
            </div>
            <div class="mt-2">
                <span class="text-green-600 font-bold text-sm">Online</span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between">
            <div>
                <h3 class="font-semibold text-lg text-gray-800">Server</h3>
                <p class="text-xs text-gray-400 mt-1">2025-08-03 12:00:00</p>
            </div>
            <div class="mt-2">
                <span class="text-green-600 font-bold text-sm">Online</span>
            </div>
        </div>
    </div>
 
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-semibold text-gray-800">MySQL Connection Usage</h2>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div class="flex-1 flex flex-col items-center justify-center">
            <div class="relative h-40 w-40 mb-6">
                <svg class="h-full w-full transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-gray-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
                    <path class="text-[rgb(var(--color-primary))]" stroke-dasharray="62, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center flex-col">
                    <span class="text-2xl font-bold text-gray-800">62%</span>
                    <span class="text-xs text-gray-500 text-center px-2">Connection Usage</span>
                </div>
            </div>
            <div class="flex items-center gap-8 w-full px-4">
                <div class="flex items-start gap-2">
                    <div class="w-3 h-3 rounded bg-[rgb(var(--color-primary)/.12)] mt-1"></div>
                    <div>
                        <div class="text-xs text-gray-500">Current Connections</div>
                        <div class="font-bold text-gray-800">250</div>
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <div class="w-3 h-3 rounded bg-blue-100 mt-1"></div>
                    <div>
                        <div class="text-xs text-gray-500">Max Connections</div>
                        <div class="font-bold text-gray-800">400</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-semibold text-gray-800">SSL Information</h2>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div class="text-sm text-gray-500 mb-6">147 days left</div>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-600">Valid</span>
                </div>
                <span class="font-medium text-gray-800">Yes</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[rgb(var(--color-primary))]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-600">Issued On</span>
                </div>
                <span class="font-medium text-gray-800">4/18/2025</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 0 1-2 2z" />
                    </svg>
                    <span class="text-gray-600">Expires On</span>
                </div>
                <span class="font-medium text-gray-800">5/19/2026</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    <span class="text-gray-600">Domain</span>
                </div>
                <span class="font-medium text-gray-800">*.heylivo.com</span>
            </div>
        </div>
    </div>
 
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Top Resource Consumers</h2>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">CPU</th>
                        <th class="px-3 py-3">MEM</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800">Nginx</td>
                        <td class="px-3 py-3 text-orange-500 font-bold">20.5%</td>
                        <td class="px-3 py-3 text-green-600 font-bold">6.2%</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800">Node</td>
                        <td class="px-3 py-3 text-red-500 font-bold">70%</td>
                        <td class="px-3 py-3 text-green-600 font-bold">5.9%</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800">Systemd</td>
                        <td class="px-3 py-3 text-green-600 font-bold">4.8%</td>
                        <td class="px-3 py-3 text-red-500 font-bold">80%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
 
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Schedulers</h2>
            <button class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800">DB Backup</td>
                        <td class="px-3 py-3 text-green-600 font-bold">37min Ago</td>
                        <td class="px-3 py-3 text-green-600 font-bold">Online</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800">Google Pay Check</td>
                        <td class="px-3 py-3 text-green-600 font-bold">3min Ago</td>
                        <td class="px-3 py-3 text-green-600 font-bold">Online</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800">Hourly Gift Return</td>
                        <td class="px-3 py-3 text-green-600 font-bold">2days</td>
                        <td class="px-3 py-3 text-red-500 font-bold">Offline</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
 </div>
 
@endsection
