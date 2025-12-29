@extends('layouts.admin')
@section('content')

<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">User Assets</h1>
        <p class="text-sm inline-block text-gray-500">{{ $user->name }} — {{ $user->email }}</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Back to Users</a>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-gray-800">Servers</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-2">IP</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servers as $s)
                        <tr class="border-t border-gray-100">
                            <td class="px-3 py-2">{{ $s->ip }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs {{ ($s->status ?? 'active') === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-700 border border-gray-200' }}">
                                    {{ $s->status ?? 'active' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-3 py-3 text-center text-sm text-gray-500">No servers configured</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-gray-800">Services</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-2">Server</th>
                        <th class="px-3 py-2">Service</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $a)
                        @php
                            $srv = $servers->firstWhere('id', $a->server_id);
                        @endphp
                        <tr class="border-t border-gray-100">
                            <td class="px-3 py-2">{{ $srv ? $srv->ip : '—' }}</td>
                            <td class="px-3 py-2">{{ strFilter($a->service_name) }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center h-6 px-2 rounded text-xs {{ ($a->status ?? 'active') === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-700 border border-gray-200' }}">
                                    {{ $a->status ?? 'active' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-3 text-center text-sm text-gray-500">No services configured</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-gray-800">Hostnames</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-3 py-2">Server</th>
                        <th class="px-3 py-2">Hostname</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hostnames as $h)
                        @php
                            $srv = $servers->firstWhere('id', $h->server_id);
                        @endphp
                        <tr class="border-t border-gray-100">
                            <td class="px-3 py-2">{{ $srv ? $srv->ip : '—' }}</td>
                            <td class="px-3 py-2">{{ $h->hostname }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-3 py-3 text-center text-sm text-gray-500">No hostnames configured</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    window.__menuActive = {!! json_encode(menuActive('users', 'users_index', '')) !!};
</script>
@endsection
