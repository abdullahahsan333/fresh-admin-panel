@extends('layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Scheduler</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: {{ now()->format('M d, H:i') }}</div>
    </div>
</header>

<!-- Scheduler Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-100">
                    <th class="px-6 py-4">Source</th>
                    <th class="px-6 py-4">Job ID</th>
                    <th class="px-6 py-4">Command</th>
                    <th class="px-6 py-4">Last Run</th>
                    <th class="px-6 py-4">Next Run</th>
                    <th class="px-6 py-4">Time Left</th>
                    <th class="px-6 py-4">Time Passed</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <!-- Row 1 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium">systemd</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">motd-news.timer</td>
                    <td class="px-6 py-4 text-gray-600">motd-news.service</td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>Mon 2025-12-22</div>
                        <div class="text-xs text-gray-400">09:23:32</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>Mon 2025-12-22</div>
                        <div class="text-xs text-gray-400">16:33:21</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 mb-1">UTC 2h 14min left</div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: 80%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>UTC 4h 55min ago</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">scheduled</span>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium">systemd</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">apt-daily.timer</td>
                    <td class="px-6 py-4 text-gray-600">apt-daily.service</td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>Sun 2025-12-21</div>
                        <div class="text-xs text-gray-400">17:00:00</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>Mon 2025-12-22</div>
                        <div class="text-xs text-gray-400">17:00:40</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 mb-1">UTC 2h 41min left</div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: 70%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>UTC 18h ago</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">scheduled</span>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium">systemd</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">dpkg-db-backup.timer</td>
                    <td class="px-6 py-4 text-gray-600">dpkg-db-backup.service</td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>Mon 2025-12-22</div>
                        <div class="text-xs text-gray-400">00:00:04</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>Tue 2025-12-23</div>
                        <div class="text-xs text-gray-400">00:00:00</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 mb-1">UTC 9h left</div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: 50%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>UTC 14h ago</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">scheduled</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection