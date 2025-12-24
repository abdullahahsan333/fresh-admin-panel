@extends('layouts.admin')

@section('content')
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Settings</h1>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Back to Dashboard</a>
    </div>
</header>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                <div class="flex items-center gap-4">
                    <img class="h-16 w-24 rounded-lg object-cover border border-[rgb(var(--color-primary))]" src="{{ !empty($site->logo) ? asset($site->logo) : 'https://via.placeholder.com/48x48?text=Logo' }}" alt="">
                    <label class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] hover:bg-[rgb(var(--color-primary)/.9)] text-white text-sm cursor-pointer">
                        <input type="file" name="logo" class="hidden" accept="image/png, image/jpeg, image/gif, image/webp">
                        Upload logo
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                <div class="flex items-center gap-4">
                    <img class="h-16 w-24 rounded-lg object-cover border border-[rgb(var(--color-primary))]" src="{{ !empty($site->favicon) ? asset($site->favicon) : 'https://via.placeholder.com/48x48?text=Fav' }}" alt="">
                    <label class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] hover:bg-[rgb(var(--color-primary)/.9)] text-white text-sm cursor-pointer">
                        <input type="file" name="favicon" class="hidden" accept="image/png, image/jpeg, image/gif, image/webp, image/x-icon">
                        Upload favicon
                    </label>
                </div>
            </div>

            <div class="lg:col-span-2">
                <hr class="my-6 border-gray-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                <input type="text" name="site_name" value="{{ old('site_name', $site->site_name ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50" placeholder="Apprise Tech Group">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Copyright</label>
                <input type="text" name="copyright" value="{{ old('copyright', $site->copyright ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50" placeholder="© 2025 Apprise Tech Group">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $site->phone ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50" placeholder="+1 202 555 0111">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address" value="{{ old('address', $site->address ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50" placeholder="Street, City, Country">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $site->email ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50" placeholder="info@example.com">
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $site->whatsapp ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Skype</label>
                <input type="text" name="skype" value="{{ old('skype', $site->skype ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Google Plus</label>
                <input type="text" name="google_plus" value="{{ old('google_plus', $site->google_plus ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                <input type="text" name="facebook" value="{{ old('facebook', $site->facebook ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Twitter</label>
                <input type="text" name="twitter" value="{{ old('twitter', $site->twitter ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                <input type="text" name="instagram" value="{{ old('instagram', $site->instagram ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                <input type="text" name="linkedin" value="{{ old('linkedin', $site->linkedin ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">YouTube</label>
                <input type="text" name="youtube" value="{{ old('youtube', $site->youtube ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $site->meta_title ?? '') }}" class="w-full pl-4 h-10 line-height-5 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                <textarea name="meta_keywords" rows="4" class="resize-none w-full pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">{{ old('meta_keywords', $site->meta_keywords ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea name="meta_description" rows="4" class="resize-none w-full pl-4 leading-6 border border-[rgb(var(--color-primary)/.35)] rounded-md shadow-sm focus:border-[rgb(var(--color-primary))] focus:outline-none focus:ring focus:ring-[rgb(var(--color-primary)/.2)] focus:ring-opacity-50">{{ old('meta_description', $site->meta_description ?? '') }}</textarea>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="inline-flex items-center justify-center h-9 px-4 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Save Settings</button>
        </div>
    </form>
</div>
@endsection
