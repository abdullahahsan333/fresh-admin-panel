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
    <!-- Projects & Assets Accordion -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-0 flex flex-col h-[600px]">
        <div class="px-4 py-3 border-b border-gray-100 border-b-gray-200 text-sm font-medium bg-gray-50 rounded-t-xl">
            <h3 class="text-lg font-semibold text-gray-800">Projects & Assets</h3>
            <p class="text-xs text-gray-500">Browse your archived assets</p>
        </div>
        <div class="flex-1 overflow-y-auto p-2">
            @if($projects->isNotEmpty())
                <ul class="space-y-1">
                    @foreach($projects as $project)
                        <li>
                            <!-- Project Header (Level 1) -->
                            <div class="flex items-center gap-2 px-2 py-2 rounded cursor-pointer hover:bg-gray-100 text-gray-800 font-semibold select-none transition-colors group" onclick="toggleAccordion(this)">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transform transition-transform duration-200 group-hover:text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2z"/></svg>
                                 <span class="uppercase tracking-wide text-xs">{{ $project->name }}</span>
                            </div>
                            <!-- Project Content (Servers) -->
                            <ul class="hidden ml-2 border-l-2 border-gray-100 pl-2 space-y-1 mt-1 mb-2">
                                @if($project->servers->isNotEmpty())
                                    @foreach($project->servers as $server)
                                        <li>
                                            <!-- Server Header (Level 2) -->
                                            <div class="flex items-center gap-2 px-2 py-1.5 rounded cursor-pointer hover:bg-gray-50 text-gray-700 text-sm select-none transition-colors group" onclick="toggleAccordion(this)">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transform transition-transform duration-200 group-hover:text-[rgb(var(--color-primary))]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="7" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="7" rx="2" ry="2"></rect><line x1="6" y1="6.5" x2="6.01" y2="6.5"></line><line x1="6" y1="17.5" x2="6.01" y2="17.5"></line></svg>
                                                 <span class="font-mono text-green-600">{{ $server->ip }}</span>
                                             </div>
                                             <!-- Server Content (Services) -->
                                             <ul class="hidden ml-2 border-l border-gray-100 pl-2 space-y-1 mt-1 mb-1">
                                                 @if($server->assets->isNotEmpty())
                                                     @foreach($server->assets as $asset)
                                                         <li>
                                                             <!-- Service Header (Level 3) -->
                                                             <div class="flex items-center gap-2 px-2 py-1.5 rounded cursor-pointer hover:bg-gray-50 text-gray-600 text-sm select-none transition-colors group" 
                                                                  onclick="toggleService(this, '{{ $server->ip }}', '{{ $asset->service_name }}')">
                                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transform transition-transform duration-200 group-hover:text-[rgb(var(--color-primary))]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 12 12 17 22 12"></polyline><polyline points="2 17 12 22 22 17"></polyline></svg>
                                                                 <span class="capitalize">{{ $asset->service_name }}</span>
                                                             </div>
                                                             <!-- Service Content (Files) - Loaded via API (Level 4) -->
                                                             <div class="hidden ml-5 mt-1" data-content-container data-loaded="false">
                                                                 <div class="px-2 py-1 text-xs text-gray-400 italic flex items-center gap-2">
                                                                    <svg class="animate-spin h-3 w-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                                    Loading files...
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="px-4 py-1 text-xs text-gray-400 italic">No services configured</li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="px-4 py-1 text-xs text-gray-400 italic">No servers configured</li>
                                @endif
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-sm text-gray-500 p-8 text-center flex flex-col items-center justify-center h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" /></svg>
                    <p>No projects found</p>
                    <p class="text-xs text-gray-400 mt-1">Add servers to your project to see them here.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Content Viewer -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-0 flex flex-col h-[600px]">
        <div class="px-4 py-3 border-b border-gray-100 border-b-gray-200 text-sm font-medium bg-gray-50 rounded-t-xl">
            <h3 class="text-lg font-semibold text-gray-800">Content</h3>
            <p class="text-xs text-gray-500">Select a file to view its contents</p>
        </div>
        <div class="flex-1 overflow-auto p-0" id="contentViewer">
            <div class="h-full flex flex-col items-center justify-center text-gray-400 p-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <p class="text-sm">Select a file from the archive tree to view details</p>
            </div>
        </div>
    </div>
</div>

<script>
    // General Accordion Toggle
    function toggleAccordion(element) {
        const content = element.nextElementSibling;
        const icon = element.querySelector('svg');
        
        if (content.classList.contains('hidden')) {
            // Open
            content.classList.remove('hidden');
            if (icon) icon.style.transform = 'rotate(90deg)';
        } else {
            // Close
            content.classList.add('hidden');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }

    // Service Toggle & API Fetch
    async function toggleService(element, ip, service) {
        const content = element.nextElementSibling; // The data-content-container div
        const icon = element.querySelector('svg');
        
        // Toggle visibility
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            if (icon) icon.style.transform = 'rotate(90deg)';
            
            // Fetch data if not already loaded
            if (content.getAttribute('data-loaded') === 'false') {
                await fetchServiceData(content, ip, service);
            }
        } else {
            content.classList.add('hidden');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }

    // Fetch Service Data Logic
    async function fetchServiceData(container, ip, service) {
        try {
            const url = "{{ route('admin.assets.archive.full') }}" + `?ip=${encodeURIComponent(ip)}&service=${encodeURIComponent(service)}`;
            const res = await fetch(url, { 
                headers: { 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest' 
                } 
            });
            
            if (!res.ok) throw new Error('HTTP ' + res.status);
            
            const json = await res.json();
            const dates = json.dates || {};
            
            if (Object.keys(dates).length === 0) {
                container.innerHTML = '<div class="px-2 py-1 text-xs text-gray-400 italic">No files found</div>';
                container.setAttribute('data-loaded', 'true');
                return;
            }

            let html = '<ul class="space-y-1">';
            
            // Build Date Groups
            Object.keys(dates).sort().reverse().forEach(function(dateKey) {
                const filesObj = dates[dateKey] || {};
                
                html += `
                    <li>
                        <div class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-50 text-gray-600 text-xs select-none" onclick="toggleAccordion(this)">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400 transform transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
                             <span class="font-medium">${dateKey}</span>
                        </div>
                        <ul class="hidden ml-4 border-l border-gray-200 pl-2 mt-1 space-y-0.5">
                `;
                
                // Build Files
                Object.keys(filesObj).forEach(function(fileName) {
                    const contentArr = filesObj[fileName] || [];
                    const contentStr = encodeURIComponent(JSON.stringify(contentArr, null, 2));
                    
                    html += `
                        <li>
                            <div class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-blue-50 hover:text-blue-600 text-gray-500 text-xs transition-colors" 
                                 onclick="showFileContent(this, '${fileName}', '${dateKey}', '${contentStr}')">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                 <span class="truncate">${fileName}</span>
                            </div>
                        </li>
                    `;
                });
                
                html += `</ul></li>`;
            });
            
            html += '</ul>';
            container.innerHTML = html;
            container.setAttribute('data-loaded', 'true');
            
        } catch (e) {
            console.error(e);
            container.innerHTML = '<div class="px-2 py-1 text-xs text-red-500">Failed to load data</div>';
        }
    }

    // Show File Content Logic
    function showFileContent(element, fileName, dateKey, encodedContent) {
        // Highlight active file
        document.querySelectorAll('[onclick^="showFileContent"]').forEach(el => {
            el.classList.remove('bg-blue-50', 'text-blue-600', 'font-medium');
            el.classList.add('text-gray-500');
        });
        element.classList.remove('text-gray-500');
        element.classList.add('bg-blue-50', 'text-blue-600', 'font-medium');

        const viewer = document.getElementById('contentViewer');
        
        try {
            const content = decodeURIComponent(encodedContent);
            viewer.innerHTML = `
                <div class="p-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-3 pb-3 border-b border-gray-100">
                        <span class="font-medium text-gray-800">${fileName}</span>
                        <span class="text-gray-300">|</span>
                        <span>${dateKey}</span>
                    </div>
                    <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg text-xs font-mono leading-relaxed overflow-x-auto whitespace-pre-wrap shadow-inner">${content}</pre>
                </div>
            `;
        } catch(e) {
            viewer.innerHTML = '<div class="p-4 text-red-500 text-sm">Error decoding file content</div>';
        }
    }
</script>
@endsection
