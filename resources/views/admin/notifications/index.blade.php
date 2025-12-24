@extends('layouts.admin')

@section('content')

<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Notifications</h1>
        <p class="text-sm inline-block text-gray-500">Recent alerts and updates</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Back to Dashboard</a>
    </div>
</header>

<div class="grid grid-cols-5 mb-6">
    <div class="col-span-1"></div>
    <div class="bg-white col-span-3 rounded-xl shadow-sm border border-gray-100">
        <div class="divide-y divide-gray-100">
            <div class="flex items-start gap-3 px-4 py-4">
                <span class="h-7 w-7 rounded bg-blue-100 text-blue-600 grid place-items-center">ℹ️</span>
                <div class="flex-1 flex justify-between">
                    <div class="font-medium text-gray-800">
                        <h3 class="text-sm">Server 128.199.73.128 CPU spike</h3>
                        <p class="text-xs text-gray-500">CPU usage is at 85%</p>
                    </div>
                    <div class="text-xs text-gray-500">2 min ago</div>
                </div>
            </div>
            <div class="flex items-start gap-3 px-4 py-4">
                <span class="h-7 w-7 rounded bg-yellow-100 text-yellow-600 grid place-items-center">⚠️</span>
                <div class="flex-1 flex justify-between">
                    <div class="font-medium text-gray-800">
                        <h3 class="text-sm">SSL expires in 65 days</h3>
                        <p class="text-xs text-gray-500">Domain: example.com</p>
                    </div>
                    <div class="text-xs text-gray-500">10 min ago</div>
                </div>
            </div>
            <div class="flex items-start gap-3 px-4 py-4">
                <span class="h-7 w-7 rounded bg-emerald-100 text-emerald-600 grid place-items-center">✅</span>
                <div class="flex-1 flex justify-between">
                    <div class="font-medium text-gray-800">
                        <h3 class="text-sm">Deployment completed</h3>
                        <p class="text-xs text-gray-500">Project: example-project</p>
                    </div>
                    <div class="text-xs text-gray-500">30 min ago</div>
                </div>
            </div>
            <div class="flex items-start gap-3 px-4 py-4">
                <span class="h-7 w-7 rounded bg-red-100 text-red-600 grid place-items-center">⛔</span>
                <div class="flex-1 flex justify-between">
                    <div class="font-medium text-gray-800">
                        <h3 class="text-sm">Redis memory high</h3>
                        <p class="text-xs text-gray-500">Memory usage is at 90%</p>
                    </div>
                    <div class="text-xs text-gray-500">1 hr ago</div>
                </div>
            </div>
            <div class="flex items-start gap-3 px-4 py-4">
                <span class="h-7 w-7 rounded bg-purple-100 text-purple-600 grid place-items-center">🔒</span>
                <div class="flex-1 flex justify-between">
                    <div class="font-medium text-gray-800">
                        <h3 class="text-sm">New admin login</h3>
                        <p class="text-xs text-gray-500">Admin: example-admin</p>
                    </div>
                    <div class="text-xs text-gray-500">2 hr ago</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
