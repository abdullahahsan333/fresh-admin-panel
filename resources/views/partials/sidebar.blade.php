<aside id="adminSidebar" class="relative overflow-hidden w-64 bg-white border-r border-gray-200 hidden lg:block min-h-screen flex flex-col">
    <div class="px-3 py-3 flex items-center">
        <div class="h-9 w-9 rounded-lg bg-emerald-500 grid place-items-center text-white">A</div>
        <span id="adminBrandText" class="font-semibold sidebar-text">Apprise Tech Group</span>
    </div>
    <nav class="px-2 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ (isset($activeMenu) && $activeMenu === 'dashboard') ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)]' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z"></path><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"></path></svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <div class="flex items-center gap-2 px-3 h-10 my-1 rounded-md {{ (isset($activeMenu) && $activeMenu === 'projects_assets') ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)] open' : 'text-gray-700 hover:bg-gray-50 close' }}" title="Projects &amp; Assets">
            <span class="sidebar-text">Projects &amp; Assets</span>
        </div>
        <div class="">
            @php
                $admin = auth('admin')->user();
                $project = $admin ? \App\Models\Project::where('admin_id', $admin->id)->first() : null;
            @endphp
            @if($project)
                @php
                    $projExpanded = (isset($activeMenu) && $activeMenu === 'projects_assets') || (isset($activeProjectId) && $activeProjectId === $project->id);
                @endphp
                <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $projExpanded ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)] open' : 'text-gray-700 hover:bg-gray-50 close' }}" aria-expanded="{{ $projExpanded ? 'true' : 'false' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6l-8 4 8 4 8-4-8-4zM4 14l8 4 8-4"/></svg>
                    <span class="sidebar-text">{{ $project->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform {{ $projExpanded ? 'rotate-180' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="{{ $projExpanded ? 'pl-4 open' : 'hidden pl-4 close' }}" data-submenu>
                    <a href="{{ route('admin.overview') }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ (isset($subMenu) && $subMenu === 'overview') ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)] open' : 'text-gray-700 hover:bg-gray-50 close' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                        <span class="sidebar-text">Overview</span>
                    </a>
                    @php
                        $servers = $project->servers()->orderBy('ip')->get();
                    @endphp
                    @foreach($servers as $server)
                        @php
                            $assets = $server->assets()->orderBy('service_name')->get();
                            $srvExpanded = isset($activeServerId) && $activeServerId === $server->id;
                        @endphp
                        <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $srvExpanded ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)]' : 'text-gray-700 hover:bg-gray-50' }}" aria-expanded="{{ $srvExpanded ? 'true' : 'false' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h12"/></svg>
                            <span class="sidebar-text">{{ $server->ip }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform {{ $srvExpanded ? 'rotate-180' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                        </a>
                        @php
                            $activeServiceLabel = isset($activeService) ? strtolower($activeService) : null;
                        @endphp
                        <div class="{{ $srvExpanded ? 'pl-4' : 'hidden pl-4' }}" data-submenu>
                            
                            @foreach ($assets as $service )
                                <a href="{{ route('admin.server.'. strtolower($service->service_name), $server->id) }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $activeServiceLabel === strtolower($service->service_name) ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)]' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l8 4-8 4-8-4 8-4zM4 10l8 4 8-4"/></svg>
                                    <span class="sidebar-text">{{ strFilter($service->service_name) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                @if($servers->isEmpty())
                    <div class="px-3 h-9 my-1 rounded-md text-gray-500">Add IPs in Assets</div>
                @endif
                <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12H3M12 3v18"/></svg>
                    <span class="sidebar-text">Utils</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="hidden pl-4" data-submenu>
                    <div class="px-3 h-9 my-1 rounded-md text-gray-700"></div>
                    @if($servers->isNotEmpty())
                        @php
                            $sslActive = isset($activeService) && $activeService === 'ssl';
                            $targetServerId = isset($activeServerId) ? $activeServerId : $servers[0]->id;
                        @endphp
                        <a href="{{ route('admin.server.ssl', $targetServerId) }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $sslActive ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)]' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l8 4-8 4-8-4 8-4zM4 10l8 4 8-4"/></svg>
                            <span class="sidebar-text">SSL</span>
                        </a>
                    @endif
                </div>
            @else
                <div class="pl-4">
                    <a href="{{ route('admin.projects.create') }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16M4 12h16"/></svg>
                        <span class="sidebar-text">Create Project</span>
                    </a>
                    <a href="{{ route('admin.assets.index') }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                        <span class="sidebar-text">Assets</span>
                    </a>
                </div>
            @endif
        </div>
        <div class="hidden pl-4" data-menu>
            <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6l-8 4 8 4 8-4-8-4zM4 14l8 4 8-4"/></svg>
                <span class="sidebar-text">Users</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
            </a>
            <div class="hidden pl-4" data-submenu>
                <a href="#" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                    <span class="sidebar-text">All Users</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16M4 12h16"/></svg>
                    <span class="sidebar-text">Create User</span>
                </a>
            </div>
            <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l8 4v8a8 8 0 11-16 0V6l8-4z"/></svg>
                <span class="sidebar-text">Settings</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
            </a>
            <div class="hidden pl-4" data-submenu>
                <a href="#" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                    <span class="sidebar-text">General</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md text-gray-700 hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l8 4-8 4-8-4 8-4zM4 10l8 4 8-4"/></svg>
                    <span class="sidebar-text">Security</span>
                </a>
            </div>
        </div>
    </nav>
    <div class="fixed bottom-0 left-0 right-0 w-64">
        <button id="sidebarProfileBtn" class="w-full flex items-center gap-1 px-2 py-2 rounded-lg hover:bg-gray-50">
            <img class="h-9 w-9 rounded-full" src="https://i.pravatar.cc/80?img=5" alt="">
            <div class="sidebar-meta flex justify-between items-center w-full">
                <div class="text-sm text-left grid grid-cols-1">
                    <div class="font-medium sidebar-text">{{ auth('admin')->user()->name ?? 'John Doe' }}</div>
                    <div class="text-gray-500 sidebar-text">Admin</div>
                </div>
                <div class="sidebar-extra ml-auto inline-block w-4 h-4 rounded-full bg-emerald-500">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M262.29 192.31a64 64 0 1 0 57.4 57.4 64.13 64.13 0 0 0-57.4-57.4zM416.39 256a154.34 154.34 0 0 1-1.53 20.79l45.21 35.46a10.81 10.81 0 0 1 2.45 13.75l-42.77 74a10.81 10.81 0 0 1-13.14 4.59l-44.9-18.08a16.11 16.11 0 0 0-15.17 1.75A164.48 164.48 0 0 1 325 400.8a15.94 15.94 0 0 0-8.82 12.14l-6.73 47.89a11.08 11.08 0 0 1-10.68 9.17h-85.54a11.11 11.11 0 0 1-10.69-8.87l-6.72-47.82a16.07 16.07 0 0 0-9-12.22 155.3 155.3 0 0 1-21.46-12.57 16 16 0 0 0-15.11-1.71l-44.89 18.07a10.81 10.81 0 0 1-13.14-4.58l-42.77-74a10.8 10.8 0 0 1 2.45-13.75l38.21-30a16.05 16.05 0 0 0 6-14.08c-.36-4.17-.58-8.33-.58-12.5s.21-8.27.58-12.35a16 16 0 0 0-6.07-13.94l-38.19-30A10.81 10.81 0 0 1 49.48 186l42.77-74a10.81 10.81 0 0 1 13.14-4.59l44.9 18.08a16.11 16.11 0 0 0 15.17-1.75A164.48 164.48 0 0 1 187 111.2a15.94 15.94 0 0 0 8.82-12.14l6.73-47.89A11.08 11.08 0 0 1 213.23 42h85.54a11.11 11.11 0 0 1 10.69 8.87l6.72 47.82a16.07 16.07 0 0 0 9 12.22 155.3 155.3 0 0 1 21.46 12.57 16 16 0 0 0 15.11 1.71l44.89-18.07a10.81 10.81 0 0 1 13.14 4.58l42.77 74a10.8 10.8 0 0 1-2.45 13.75l-38.21 30a16.05 16.05 0 0 0-6.05 14.08c.33 4.14.55 8.3.55 12.47z"></path></svg>
                </div>
            </div>
        </button>
    </div>
</aside>
