@extends('layouts.user')
@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between">
        <div class="text-xl font-semibold">My Profile</div>
        <a href="{{ route('user.logout') }}" class="text-sm text-red-600 hover:underline">Log Out</a>
    </div>
</div>

<form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo (Avatar)</label>
        <div class="flex items-center gap-2">
            <img class="h-16 w-16 rounded-lg object-cover border border-gray-200" src="{{ (($webUser->avatar ?? null) ? asset($webUser->avatar) : (($user->avatar ?? null) ? asset($user->avatar) : 'https://i.pravatar.cc/80?img=5')) }}" alt="">
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
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Name</label>
                <input type="text" name="name" value="{{ old('name', $webUser->name ?? '') }}" class="mt-1 w-full h-10 border rounded-lg px-3">
            </div>
            <div>
                <label class="text-sm text-gray-600">Email</label>
                <input type="text" name="email" value="{{ old('email', $webUser->email ?? '') }}" readonly class="mt-1 w-full h-10 border rounded-lg px-3 bg-gray-100 text-gray-500 border-gray-200 cursor-not-allowed focus:outline-none focus:ring-0 focus:border-gray-200">
            </div>
            <div>
                <label class="text-sm text-gray-600">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $webUser->phone ?? '') }}" class="mt-1 w-full h-10 border rounded-lg px-3">
            </div>
            <div class="col-span-2">
                <label class="text-sm text-gray-600">Address</label>
                <input type="text" name="address" value="{{ old('address', $webUser->address ?? '') }}" class="mt-1 w-full h-10 border rounded-lg px-3">
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</form>

@push('footer_scripts')
<script>
    (function(){
        const resetBtn = document.getElementById('resetAvatarBtn');
        const fileInput = document.querySelector('input[name="avatar"]');
        if (resetBtn && fileInput) {
            resetBtn.addEventListener('click', function(){ fileInput.value = ''; });
        }
    })();
</script>
@endpush

@endsection
