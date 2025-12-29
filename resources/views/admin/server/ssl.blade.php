@extends('layouts.admin')

@section('content')
<!-- Top bar -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">SSL</h1>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">Last updated: {{ now()->format('M d, H:i') }}</div>
    </div>
</header>

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Left Column: Monitored Domains -->
    <div class="w-full lg:w-80">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 h-full flex flex-col">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[rgb(var(--color-primary))]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <h2 class="font-semibold text-gray-800">Monitored Domains</h2>
            </div>
            
            <div id="sslTabs" class="p-3 space-y-2 overflow-y-auto flex-1">
                @if(isset($hostnames) && $hostnames->count() > 0)
                    @foreach($hostnames as $hostname)
                        <div class="p-3 rounded-lg cursor-pointer border border-gray-100 hover:border-[rgb(var(--color-primary)/.30)] hover:bg-gray-50 hover:text-[rgb(var(--color-primary))] group transition-all" data-ssl-key="{{ Str::slug($hostname->hostname) }}" data-hostname="{{ $hostname->hostname }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-2 rounded-full bg-[rgb(var(--color-primary))]"></div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-700">{{ $hostname->hostname }}</div>
                                        <div class="text-xs text-gray-400 group-hover:text-gray-500">Click to check SSL</div>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-3 rounded-lg cursor-pointer border border-gray-100 hover:border-[rgb(var(--color-primary)/.30)] hover:bg-gray-50 hover:text-[rgb(var(--color-primary))] group transition-all" data-ssl-key="facebook-com" data-hostname="facebook.com">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full bg-[rgb(var(--color-primary))]"></div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-700">facebook.com</div>
                                    <div class="text-xs text-gray-400 group-hover:text-gray-500">Click to check SSL</div>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: SSL Details -->
    <div class="flex-1 min-w-0 h-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div id="sslEmpty" class="py-24 text-center text-gray-500 text-sm">Select a domain to view SSL information</div>
            <div id="sslPanel" class="hidden">
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-[rgb(var(--color-primary)/.06)] flex items-center justify-center text-[rgb(var(--color-primary))]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h1 id="sslDomainTitleAdmin" class="text-2xl font-bold text-gray-900">google.com</h1>
                            <p class="text-sm text-gray-500">SSL Certificate Information</p>
                        </div>
                    </div>
                    <button class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        Copy All
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="rounded-xl p-5 border border-gray-200 bg-white">
                        <div class="text-xs font-semibold text-gray-500 mb-2">STATUS</div>
                        <div class="flex items-center gap-2"><div class="h-2 w-2 rounded-full bg-amber-500"></div><div class="text-base font-semibold text-amber-700">Expiring Soon</div></div>
                    </div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white">
                        <div class="text-xs font-semibold text-gray-500 mb-2">DAYS REMAINING</div>
                        <div class="text-base font-semibold text-[rgb(var(--color-primary))]">65 days</div>
                    </div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white">
                        <div class="text-xs font-semibold text-gray-500 mb-2">EXPIRES ON</div>
                        <div class="text-base font-semibold text-[rgb(var(--color-primary))]">Feb 25</div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200">
                    <div class="px-5 py-3 flex items-center gap-2 border-b border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[rgb(var(--color-primary))]" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-sm font-semibold text-gray-800">Certificate Details</div>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Issuer</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">C=US, O=Google Trust Services, CN=WR2</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Subject</div><div id="sslSubjectCnAdmin" class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">CN=*.google.com</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid From</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">December 3, 2025</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid Until</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">February 25, 2026</div></div>
                        <div class="space-y-1 md:col-span-2"><div class="text-xs font-medium text-gray-500 uppercase">Serial Number</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">56C74F44A4CE9B190AD981A4FC39C286</div></div>
                    </div>
                </div>
            </div>
            <div id="sslPanel-heylivo-com" class="hidden">
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-[rgb(var(--color-primary)/.06)] flex items-center justify-center text-[rgb(var(--color-primary))]"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg></div>
                        <div><h1 class="text-2xl font-bold text-gray-900">heylivo.com</h1><p class="text-sm text-gray-500">SSL Certificate Information</p></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">STATUS</div><div class="flex items-center gap-2"><div class="h-2 w-2 rounded-full bg-emerald-500"></div><div class="text-base font-semibold text-emerald-700">Valid</div></div></div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">DAYS REMAINING</div><div class="text-base font-semibold text-[rgb(var(--color-primary))]">147 days</div></div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">EXPIRES ON</div><div class="text-base font-semibold text-[rgb(var(--color-primary))]">May 19</div></div>
                </div>
                <div class="rounded-xl border border-gray-200">
                    <div class="px-5 py-3 flex items-center gap-2 border-b border-gray-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[rgb(var(--color-primary))]" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1"/></svg><div class="text-sm font-semibold text-gray-800">Certificate Details</div></div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Issuer</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">C=US, O=Let's Encrypt, CN=R3</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Subject</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">CN=heylivo.com</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid From</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">October 2, 2025</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid Until</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">April 28, 2026</div></div>
                        <div class="space-y-1 md:col-span-2"><div class="text-xs font-medium text-gray-500 uppercase">Serial Number</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">03AFB65C1C9E44B1A1D0E3F4A6C2B110</div></div>
                    </div>
                </div>
            </div>
            <div id="sslPanel-facebook-com" class="hidden">
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-[rgb(var(--color-primary)/.06)] flex items-center justify-center text-[rgb(var(--color-primary))]"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg></div>
                        <div><h1 class="text-2xl font-bold text-gray-900">facebook.com</h1><p class="text-sm text-gray-500">SSL Certificate Information</p></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">STATUS</div><div class="flex items-center gap-2"><div class="h-2 w-2 rounded-full bg-amber-500"></div><div class="text-base font-semibold text-amber-700">Expiring Soon</div></div></div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">DAYS REMAINING</div><div class="text-base font-semibold text-[rgb(var(--color-primary))]">7 days</div></div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">EXPIRES ON</div><div class="text-base font-semibold text-[rgb(var(--color-primary))]">Jan 1</div></div>
                </div>
                <div class="rounded-xl border border-gray-200">
                    <div class="px-5 py-3 flex items-center gap-2 border-b border-gray-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[rgb(var(--color-primary))]" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1"/></svg><div class="text-sm font-semibold text-gray-800">Certificate Details</div></div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Issuer</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">C=US, O=DigiCert Inc, CN=DigiCert Global G2 TLS RSA SHA256 2020 CA1</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Subject</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">C=US, ST=California, L=Menlo Park, O=Meta Platforms, Inc., CN=*.facebook.com</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid From</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">October 2, 2025</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid Until</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">January 1, 2026</div></div>
                        <div class="space-y-1 md:col-span-2"><div class="text-xs font-medium text-gray-500 uppercase">Serial Number</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">056E989107048925CEABC39545D6E652</div></div>
                    </div>
                </div>
            </div>
            <div id="sslPanel-apprise-it" class="hidden">
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-[rgb(var(--color-primary)/.06)] flex items-center justify-center text-[rgb(var(--color-primary))]"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg></div>
                        <div><h1 class="text-2xl font-bold text-gray-900">apprise.it</h1><p class="text-sm text-gray-500">SSL Certificate Information</p></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">STATUS</div><div class="flex items-center gap-2"><div class="h-2 w-2 rounded-full bg-red-500"></div><div class="text-base font-semibold text-red-700">Expired</div></div></div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">DAYS REMAINING</div><div class="text-base font-semibold text-[rgb(var(--color-primary))]">0 days</div></div>
                    <div class="rounded-xl p-5 border border-gray-200 bg-white"><div class="text-xs font-semibold text-gray-500 mb-2">EXPIRES ON</div><div class="text-base font-semibold text-[rgb(var(--color-primary))]">Dec 10</div></div>
                </div>
                <div class="rounded-xl border border-gray-200">
                    <div class="px-5 py-3 flex items-center gap-2 border-b border-gray-200"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[rgb(var(--color-primary))]" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1"/></svg><div class="text-sm font-semibold text-gray-800">Certificate Details</div></div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Issuer</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">C=GB, O=GlobalSign, CN=GlobalSign RSA OV CA 2018</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Subject</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">CN=apprise.it</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid From</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">June 14, 2025</div></div>
                        <div class="space-y-1"><div class="text-xs font-medium text-gray-500 uppercase">Valid Until</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800">December 10, 2025</div></div>
                        <div class="space-y-1 md:col-span-2"><div class="text-xs font-medium text-gray-500 uppercase">Serial Number</div><div class="bg-white border border-gray-200 rounded px-4 py-2 text-sm text-gray-800 font-mono">A1B2C3D4E5F60718273645AABBCCDDEE</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer_scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = Array.from(document.querySelectorAll('#sslTabs [data-ssl-key]'));
        const empty = document.getElementById('sslEmpty');
        const panel = document.getElementById('sslPanel');
        const titleEl = document.getElementById('sslDomainTitleAdmin');
        const subjectEl = document.getElementById('sslSubjectCnAdmin');
        const setActiveTab = (tab) => {
            tabs.forEach(t => {
                const isActive = t === tab;
                const cls = t.classList;
                if (isActive) {
                    cls.add('bg-[rgb(var(--color-primary)/.10)]','border-[rgb(var(--color-primary)/.25)]','text-[rgb(var(--color-primary))]','cursor-default');
                    cls.remove('hover:bg-gray-50','hover:text-[rgb(var(--color-primary))]','hover:border-[rgb(var(--color-primary)/.30)]');
                } else {
                    cls.remove('bg-[rgb(var(--color-primary)/.10)]','border-[rgb(var(--color-primary)/.25)]','text-[rgb(var(--color-primary))]','cursor-default');
                    cls.add('hover:bg-gray-50','hover:text-[rgb(var(--color-primary))]','hover:border-[rgb(var(--color-primary)/.30)]');
                }
            });
            const domain = tab.getAttribute('data-hostname') || '';
            if (titleEl) titleEl.textContent = domain || 'domain.com';
            if (subjectEl) subjectEl.textContent = 'CN=' + (domain ? '*.' + domain : '*.domain.com');
            if (panel) panel.classList.remove('hidden');
            if (empty) empty.classList.add('hidden');
        };
        tabs.forEach(t => t.addEventListener('click', () => setActiveTab(t)));
        if (tabs.length > 0) {
            setActiveTab(tabs[0]);
        }
    });
</script>
@endpush
