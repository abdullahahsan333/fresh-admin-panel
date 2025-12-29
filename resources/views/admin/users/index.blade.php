@extends('layouts.admin')
@section('content')

<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Users</h1>
        <p class="text-sm inline-block text-gray-500">All registered users</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Create User</a>
    </div>
</header>

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-700">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Joined</th>
                    <th class="px-4 py-2">Status Update</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-2">{{ $u->name }}</td>
                        <td class="px-4 py-2">{{ $u->email }}</td>
                        <td class="px-4 py-2">
                            @php
                                $stRaw = $u->status ?? 1;
                                $st = is_numeric($stRaw) ? intval($stRaw) : (strtolower($stRaw) === 'pending' ? 0 : (strtolower($stRaw) === 'suspended' ? 2 : 1));
                                $label = ($st === 0) ? 'pending' : (($st === 1) ? 'active' : 'suspended');
                                $style = $st === 1 ? 'bg-green-50 text-green-700 border border-green-200' : ($st === 0 ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-red-50 text-red-700 border border-red-200');
                            @endphp
                            <span class="inline-flex items-center h-6 px-2 rounded text-xs {{ $style }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $u->created_at ? $u->created_at->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('admin.users.status', $u->id) }}" class="inline-flex items-center gap-2 statusForm">
                                @csrf
                                <select name="status" class="h-8 text-xs border rounded-md px-2 statusSelect">
                                    <option value="0" {{ $st === 0 ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ $st === 1 ? 'selected' : '' }}>Active</option>
                                    <option value="2" {{ $st === 2 ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.users.assets', $u->id) }}" class="inline-grid place-items-center h-8 w-8 rounded-lg bg-[rgb(var(--color-primary))] text-white" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.5 12s4.5-7.5 10.5-7.5S22.5 12 22.5 12s-4.5 7.5-10.5 7.5S1.5 12 1.5 12z"/>
                                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 text-xs text-gray-500">Total: {{ $users->count() }}</div>
    <script>
        window.__menuActive = {!! json_encode(menuActive('users', 'users_index', '')) !!};
        (function(){
            var selects = document.querySelectorAll('.statusSelect');
            for (var i = 0; i < selects.length; i++) {
                selects[i].addEventListener('change', function(e){
                    var f = e.target.closest('form');
                    if (f) f.submit();
                });
            }
        })();
    </script>
@endsection
