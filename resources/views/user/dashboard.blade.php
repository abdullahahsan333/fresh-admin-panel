@extends('layouts.user')
@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="text-xl font-semibold">Welcome {{ strFilter(Auth::user()->name) }}</div>
    <p class="mt-2 text-sm text-gray-600">Welcome to user panel</p>
</div>

@endsection
