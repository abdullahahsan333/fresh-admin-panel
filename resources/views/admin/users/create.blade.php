@extends('layouts.admin')
@section('content')

<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Create User</h1>
        <p class="text-sm inline-block text-gray-500">Add a new user</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Back to Users</a>
    </div>
    <script>
        window.__menuActive = {!! json_encode(menuActive('users', 'users_create', '')) !!};
    </script>
</header>

<div class="">
    <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo (Avatar)</label>
            <div class="flex items-center gap-2">
                <img class="h-16 w-16 rounded-lg object-cover border border-gray-200" src="https://i.pravatar.cc/80?img=5" alt="">
                <div class="space-x-2">
                    <label class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] hover:bg-[rgb(var(--color-primary)/.9)] text-white text-sm cursor-pointer">
                        <input type="file" name="avatar" class="hidden" accept="image/png, image/jpeg, image/gif, image/webp">
                        Upload Avatar
                    </label>
                    <button type="button" id="resetAvatarBtn" class="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-gray-300 text-gray-700 text-sm">Reset</button>
                    <div class="text-xs text-gray-500 mt-2">Allowed JPG, GIF or PNG. Max size of 800K</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full h-10 pl-4 border rounded-md focus:border-[rgb(var(--color-primary))] focus:outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full h-10 pl-4 border rounded-md focus:border-[rgb(var(--color-primary))] focus:outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full h-10 pl-4 border rounded-md focus:border-[rgb(var(--color-primary))] focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address" value="{{ old('address') }}" class="w-full h-10 pl-4 border rounded-md focus:border-[rgb(var(--color-primary))] focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" class="w-full h-10 pl-4 border rounded-md focus:border-[rgb(var(--color-primary))] focus:outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" class="w-full h-10 pl-4 border rounded-md focus:border-[rgb(var(--color-primary))] focus:outline-none" required>
            </div>
            </div>
            <div class="flex items-center justify-start mt-4">
                <button type="submit" class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Save User</button>
            </div>
        </div>
    </form>
    <script>
        (function(){
            const resetBtn = document.getElementById('resetAvatarBtn');
            const fileInput = document.querySelector('input[name="avatar"]');
            if (resetBtn && fileInput) {
                resetBtn.addEventListener('click', function(){ fileInput.value = ''; });
            }
        })();
    </script>
</div>
@endsection
