@extends('layouts.admin')
@section('content')

<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10 sticky top-0 mb-6">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Asset Archive</h1>
        <p class="text-sm inline-block text-gray-500">Browse archived asset snapshots</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center h-9 px-3 rounded-lg bg-[rgb(var(--color-primary))] text-white text-sm">Back to Dashboard</a>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-0">
        <div class="px-4 py-3 border-b border-gray-100 text-sm font-medium">Projects & Assets</div>
        <div class="h-[560px] overflow-y-auto p-2">
            @if (!empty($archiveTree))
                <ul class="space-y-1">
                    @foreach($archiveTree as $proj)
                        <li>
                            <button type="button" class="w-full px-2 py-1 rounded text-sm font-medium text-gray-800 flex items-center gap-2 hover:bg-gray-50" data-tree-toggle data-key="p:{{ $proj['project'] }}" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 transform transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h5l2 2h11v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span>{{ $proj['project'] }}</span>
                            </button>
                            <div class="ml-6 hidden" data-tree-children>
                                @foreach($proj['servers'] as $srv)
                                    <div class="my-1">
                                        <button type="button" class="w-full px-2 py-1 rounded text-xs text-gray-700 flex items-center gap-2 hover:bg-gray-50" data-tree-toggle data-key="s:{{ $proj['project'] }}:{{ $srv['ip'] }}" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 transform transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <span class="inline-block w-2 h-2 rounded-full bg-orange-500"></span>
                                            <span>Server ({{ $srv['ip'] }})</span>
                                        </button>
                                        <div class="ml-6 hidden" data-tree-children>
                                            @foreach($srv['services'] as $svc)
                                                <div class="my-1">
                                                    <button type="button" class="w-full px-2 py-1 rounded text-xs text-gray-700 flex items-center gap-2 hover:bg-gray-50" data-tree-toggle data-key="v:{{ $proj['project'] }}:{{ $srv['ip'] }}:{{ $svc['name'] }}" aria-expanded="false" data-fetch-service data-ip="{{ $srv['ip'] }}" data-service="{{ strtolower($svc['name']) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 transform transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h16v16H4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        <span>{{ strtolower($svc['name']) }}</span>
                                                    </button>
                                                    <div class="ml-6 hidden" data-tree-children>
                                                        @foreach($svc['dates'] as $dt)
                                                            <div class="my-1">
                                                                <button type="button" class="w-full px-2 py-1 rounded text-xs text-gray-600 flex items-center gap-2 hover:bg-gray-50" data-tree-toggle data-key="d:{{ $proj['project'] }}:{{ $srv['ip'] }}:{{ $svc['name'] }}:{{ $dt['date'] }}" aria-expanded="false">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 transform transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h18M3 12h18M3 17h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                    <span>{{ $dt['date'] }}</span>
                                                                </button>
                                                                <div class="ml-6 hidden" data-tree-children>
                                                                    @foreach($dt['files'] as $f)
                                                                        <a href="{{ route('admin.assets.archive') }}?file={{ urlencode($f['rel']) }}" class="block px-2 py-1 rounded text-xs text-gray-700 hover:bg-gray-50" data-file-entry data-file-name="{{ $f['name'] }}">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block align-middle h-4 w-4 text-gray-500 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 2v6h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                                            <span>{{ $f['name'] }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-3 text-sm text-gray-500">No archive found</div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-0">
        <div class="px-4 py-3 border-b border-gray-100 text-sm font-medium">Content</div>
        <div class="p-4">
            @if ($selectedContent)
                <div class="text-sm text-gray-500 mb-2">
                    View: {{ $selectedMeta['file'] ?? '' }}
                </div>
                <pre class="bg-gray-900 text-white text-xs leading-5 whitespace-pre-wrap">{{ $selectedContent }}</pre>
            @else
                <div class="text-sm text-gray-500">Select a file from the archive tree to view</div>
            @endif
        </div>
    </div>
</div>

<script>
    window.__menuActive = {!! json_encode(menuActive('projects_assets', 'archive', '')) !!};
    (function(){
        var toggles = document.querySelectorAll('[data-tree-toggle]');
        for (var i = 0; i < toggles.length; i++) {
            (function(btn){
                var key = btn.getAttribute('data-key');
                var panel = btn.nextElementSibling;
                var saved = key ? localStorage.getItem('archiveTree.' + key) : null;
                if (saved === '1' && panel) {
                    panel.classList.remove('hidden');
                    btn.setAttribute('aria-expanded', 'true');
                    var arrow = btn.querySelector('svg.h-4.w-4.text-gray-600');
                    if (arrow) arrow.style.transform = 'rotate(90deg)';
                }
                btn.addEventListener('click', function(){
                    if (!panel) return;
                    var isHidden = panel.classList.contains('hidden');
                    if (isHidden) {
                        panel.classList.remove('hidden');
                        btn.setAttribute('aria-expanded', 'true');
                        var arrow = btn.querySelector('svg.h-4.w-4.text-gray-600');
                        if (arrow) arrow.style.transform = 'rotate(90deg)';
                        if (key) localStorage.setItem('archiveTree.' + key, '1');
                    } else {
                        panel.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                        var arrow2 = btn.querySelector('svg.h-4.w-4.text-gray-600');
                        if (arrow2) arrow2.style.transform = 'rotate(0deg)';
                        if (key) localStorage.setItem('archiveTree.' + key, '0');
                    }
                });
            })(toggles[i]);
        }

        // Dynamic loading of service archive from API
        const serviceButtons = document.querySelectorAll('[data-fetch-service]');
        serviceButtons.forEach(function(btn){
            btn.addEventListener('click', async function(){
                const ip = btn.getAttribute('data-ip');
                const service = btn.getAttribute('data-service');
                const container = btn.nextElementSibling;
                if (!container) return;
                // If already populated once, skip re-fetch
                if (container.getAttribute('data-populated') === '1') return;
                container.innerHTML = '<div class="px-2 py-2 text-xs text-gray-500">Loading...</div>';
                try {
                    const url = "{{ route('admin.assets.archive.full') }}" + `?ip=${encodeURIComponent(ip)}&service=${encodeURIComponent(service)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const json = await res.json();
                    const dates = json.dates || {};
                    let html = '';
                    // Build date -> files
                    Object.keys(dates).sort().forEach(function(dateKey){
                        const filesObj = dates[dateKey] || {};
                        html += `
                            <div class="my-1">
                                <button type="button" class="w-full px-2 py-1 rounded text-xs text-gray-600 flex items-center gap-2 hover:bg-gray-50" data-tree-toggle aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 transform transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h18M3 12h18M3 17h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span>${dateKey}</span>
                                </button>
                                <div class="ml-6 hidden" data-tree-children>
                        `;
                        Object.keys(filesObj).forEach(function(fileName){
                            const contentArr = filesObj[fileName] || [];
                            const contentStr = JSON.stringify(contentArr, null, 2);
                            html += `
                                <a href="javascript:void(0)" class="block px-2 py-1 rounded text-xs text-gray-700 hover:bg-gray-50" data-archive-content data-file="${encodeURIComponent(fileName)}" data-date="${encodeURIComponent(dateKey)}" data-json="${encodeURIComponent(contentStr)}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block align-middle h-4 w-4 text-gray-500 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 2v6h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    <span>${fileName}</span>
                                </a>
                            `;
                        });
                        html += `</div></div>`;
                    });
                    container.innerHTML = html || '<div class="px-2 py-2 text-xs text-gray-500">No data</div>';
                    container.setAttribute('data-populated', '1');
                    // Rebind toggles for newly added nodes
                    var innerToggles = container.querySelectorAll('[data-tree-toggle]');
                    innerToggles.forEach(function(it){
                        it.addEventListener('click', function(){
                            const p = it.nextElementSibling;
                            if (!p) return;
                            const hidden = p.classList.contains('hidden');
                            p.classList.toggle('hidden');
                            it.setAttribute('aria-expanded', hidden ? 'true' : 'false');
                            const arrow = it.querySelector('svg.h-4.w-4.text-gray-600');
                            if (arrow) arrow.style.transform = hidden ? 'rotate(90deg)' : 'rotate(0deg)';
                        });
                    });
                    // Bind file click to show content
                    const fileLinks = container.querySelectorAll('[data-archive-content]');
                    fileLinks.forEach(function(a){
                        a.addEventListener('click', function(){
                            const jsonEncoded = a.getAttribute('data-json') || '';
                            try {
                                const content = decodeURIComponent(jsonEncoded);
                                const viewer = document.querySelector('.bg-white.rounded-xl.shadow-sm.border.border-gray-100.p-0 + .bg-white') || document.querySelector('.bg-white.rounded-xl.shadow-sm.border.border-gray-100.p-0');
                                const contentBox = document.querySelector('.bg-white.rounded-xl.shadow-sm.border.border-gray-100.p-0 ~ .bg-white') || null;
                                // Update content panel
                                const panel = document.querySelector('.grid .bg-white.rounded-xl.shadow-sm.border.border-gray-100.p-0:nth-child(2) .p-4');
                                if (panel) {
                                    panel.innerHTML = `
                                        <div class="text-sm text-gray-500 mb-2">View: ${a.getAttribute('data-date')} / ${a.getAttribute('data-file')}</div>
                                        <pre class="bg-gray-900 text-white text-xs leading-5 whitespace-pre-wrap">${content}</pre>
                                    `;
                                }
                            } catch(e) {}
                        });
                    });
                } catch (e) {
                    container.innerHTML = '<div class="px-2 py-2 text-xs text-red-600">Failed to load</div>';
                }
            });
        });
    })();
</script>
@endsection
