@extends('layouts.admin')

@section('content')
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-3">
        <div class="h-8 w-8 rounded-lg text-white grid place-items-center">👤</div>
        <h1 class="text-lg font-semibold text-gray-800">My Profile</h1>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Back to Dashboard</a>
    </div>
</header>

<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            <div class="">
                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo (Avatar)</label>
                <div class="flex items-center gap-2">
                    <img class="h-16 w-16 rounded-lg object-cover border border-gray-200" src="{{ ($admin->avatar ?? null) ? asset($admin->avatar) : 'https://i.pravatar.cc/80?img=5' }}" alt="">
                    <div class="space-x-2">
                        <label class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] hover:bg-[rgb(var(--color-primary)/.9)] text-white text-sm cursor-pointer">
                            <input type="file" name="avatar" class="hidden" accept="image/png, image/jpeg, image/gif, image/webp">
                            Upload new photo
                        </label>
                        <button type="button" id="resetAvatarBtn" class="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-gray-300 text-gray-700 text-sm">Reset</button>
                        <div class="text-xs text-gray-500 mt-2">Allowed JPG, GIF or PNG. Max size of 800K</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', explode(' ', $admin->name ?? '')[0] ?? '') }}" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', explode(' ', $admin->name ?? '')[1] ?? '') }}" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">UserName</label>
                    <input type="text" name="username" value="{{ old('username', $admin->username ?? '') }}" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $admin->phone ?? '') }}" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50" placeholder="+1 202 555 0111">  
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" value="{{ old('address', $admin->address ?? '') }}" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50" placeholder="Address">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                    <select name="timezone" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
                        <option value="">Select Timezone</option>
                        <option {{ $admin->timezone == 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option {{ $admin->timezone == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka</option>
                        <option {{ $admin->timezone == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <select name="currency" class="w-full h-10 line-height-5 pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
                        <option value="">Select Currency</option>
                        <option {{ $admin->currency == 'USD' ? 'selected' : '' }}>USD</option>
                        <option {{ $admin->currency == 'BDT' ? 'selected' : '' }}>BDT</option>
                        <option {{ $admin->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                </div>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button type="submit" class="inline-flex items-center justify-center h-9 px-4 rounded-lg bg-[rgb(var(--color-primary))] hover:bg-[rgb(var(--color-primary)/.9)] text-white text-sm">Save changes</button>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center h-9 px-4 rounded-lg border border-gray-300 text-gray-700 text-sm">Cancel</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-sm font-semibold text-gray-800 mb-3">Delete Account</div>
        <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-800 p-3 text-xs mb-3">
            Are you sure you want to delete your account? Once you delete your account, there is no going back. Please be certain.
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700 mb-3">
            <input id="confirmDeactivation" type="checkbox" class="rounded border-gray-300">
            <span>I confirm my account deactivation</span>
        </label>
        <button id="deactivateBtn" type="button" class="inline-flex items-center justify-center h-9 px-4 rounded-lg bg-red-600 text-white text-sm opacity-50 cursor-not-allowed" disabled>Deactivate Account</button>
    </div>
</div>
@endsection
@push('footer_scripts')
<script>
    (function(){
        const resetBtn = document.getElementById('resetAvatarBtn');
        const fileInput = document.querySelector('input[name="avatar"]');
        if (resetBtn && fileInput) {
            resetBtn.addEventListener('click', () => { fileInput.value = ''; });
        }
        const confirmCb = document.getElementById('confirmDeactivation');
        const deactivateBtn = document.getElementById('deactivateBtn');
        if (confirmCb && deactivateBtn) {
            confirmCb.addEventListener('change', () => {
                const enabled = confirmCb.checked;
                deactivateBtn.disabled = !enabled;
                deactivateBtn.classList.toggle('opacity-50', !enabled);
                deactivateBtn.classList.toggle('cursor-not-allowed', !enabled);
            });
        }
    })();
</script>
@endpush
