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
        <div class="text-md font-medium">Avatar</div>
        <div class="mt-4 flex items-center gap-4">
            <img class="h-16 w-16 rounded-full object-cover border" src="{{ ($webUser && !empty($webUser->avatar)) ? asset($webUser->avatar) : 'https://i.pravatar.cc/80?img=5' }}" alt="">
            <div>
                <input type="file" name="avatar" class="block text-sm">
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

@endsection
