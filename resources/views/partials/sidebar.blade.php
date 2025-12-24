<aside id="adminSidebar" class="relative overflow-hidden bg-white border-r border-gray-200 hidden lg:block min-h-screen flex flex-col">
    <div class="px-3 py-3 flex items-center">
        @if(!empty($site->logo))
            <img class="h-9 w-9 rounded-lg object-cover border border-gray-200" src="{{ asset($site->logo) }}" alt="">
        @else
            <div class="h-9 w-9 rounded-lg bg-emerald-500 grid place-items-center text-white">{{ strtoupper(substr($site->site_name ?? 'A', 0, 1)) }}</div>
        @endif
        <span id="adminBrandText" class="font-semibold sidebar-text">{{ $site->site_name ?? 'Admin' }}</span>
        <button id="mobileSidebarCloseBtn" class="ml-auto inline-flex items-center justify-center h-9 w-9 rounded-lg hover:bg-gray-100 lg:hidden" title="Close">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="px-2 space-y-1 mb-7">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ (isset($activeMenu) && $activeMenu === 'dashboard') ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)]' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z"></path><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"></path></svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <div class="flex items-center justify-around gap-2 px-3 my-1 rounded-md text-gray-700" title="Projects &amp; Assets">
            <div class="sidebar-text text-xs">
                Projects &amp; Assets
            </div>
            <span class="border border-b-1 border-gray-400 border-r-0 border-l-0 border-t-0 w-[40%] transform -translate-y-1/2"></span>
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
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-width="2" d="M10,23 C5.02943725,23 1,18.9705627 1,14 C1,9.02943725 5.02943725,5 10,5 L10,14 C10,14 10.7746374,14.7746374 11.1619561,15.1619561 L16.363961,20.363961 C14.7352814,21.9926407 12.4852814,23 10,23 Z M14,10 L14,1 C18.9705627,1 23,5.02943725 23,10 L14,10 Z M14,13 L22,13 C22,15.2037225 21.2079601,17.2224541 19.8930183,18.7870568 L14,13 Z"></path></svg>
                    <span class="sidebar-text">{{ $project->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform {{ $projExpanded ? 'rotate-180' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="{{ $projExpanded ? 'pl-4 open' : 'hidden pl-4 close' }}" data-submenu>
                    <a href="{{ route('admin.overview') }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ (isset($subMenu) && $subMenu === 'overview') ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)] open' : 'text-gray-700 hover:bg-gray-50 close' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="#D9D9D9" d="M512 188c-99.3 0-192.7 38.7-263 109-70.3 70.2-109 163.6-109 263 0 105.6 44.5 205.5 122.6 276h498.8A371.12 371.12 0 0 0 884 560c0-99.3-38.7-192.7-109-263-70.2-70.3-163.6-109-263-109zm-30 44c0-4.4 3.6-8 8-8h44c4.4 0 8 3.6 8 8v80c0 4.4-3.6 8-8 8h-44c-4.4 0-8-3.6-8-8v-80zM270 582c0 4.4-3.6 8-8 8h-80c-4.4 0-8-3.6-8-8v-44c0-4.4 3.6-8 8-8h80c4.4 0 8 3.6 8 8v44zm90.7-204.4l-31.1 31.1a8.03 8.03 0 0 1-11.3 0l-56.6-56.6a8.03 8.03 0 0 1 0-11.3l31.1-31.1c3.1-3.1 8.2-3.1 11.3 0l56.6 56.6c3.1 3.1 3.1 8.2 0 11.3zm291.1 83.5l-84.5 84.5c5 18.7.2 39.4-14.5 54.1a55.95 55.95 0 0 1-79.2 0 55.95 55.95 0 0 1 0-79.2 55.87 55.87 0 0 1 54.1-14.5l84.5-84.5c3.1-3.1 8.2-3.1 11.3 0l28.3 28.3c3.1 3.1 3.1 8.2 0 11.3zm43-52.4l-31.1-31.1a8.03 8.03 0 0 1 0-11.3l56.6-56.6c3.1-3.1 8.2-3.1 11.3 0l31.1 31.1c3.1 3.1 3.1 8.2 0 11.3l-56.6 56.6a8.03 8.03 0 0 1-11.3 0zM846 538v44c0 4.4-3.6 8-8 8h-80c-4.4 0-8-3.6-8-8v-44c0-4.4 3.6-8 8-8h80c4.4 0 8 3.6 8 8z"></path><path d="M623.5 421.5a8.03 8.03 0 0 0-11.3 0L527.7 506c-18.7-5-39.4-.2-54.1 14.5a55.95 55.95 0 0 0 0 79.2 55.95 55.95 0 0 0 79.2 0 55.87 55.87 0 0 0 14.5-54.1l84.5-84.5c3.1-3.1 3.1-8.2 0-11.3l-28.3-28.3zM490 320h44c4.4 0 8-3.6 8-8v-80c0-4.4-3.6-8-8-8h-44c-4.4 0-8 3.6-8 8v80c0 4.4 3.6 8 8 8z"></path><path d="M924.8 385.6a446.7 446.7 0 0 0-96-142.4 446.7 446.7 0 0 0-142.4-96C631.1 123.8 572.5 112 512 112s-119.1 11.8-174.4 35.2a446.7 446.7 0 0 0-142.4 96 446.7 446.7 0 0 0-96 142.4C75.8 440.9 64 499.5 64 560c0 132.7 58.3 257.7 159.9 343.1l1.7 1.4c5.8 4.8 13.1 7.5 20.6 7.5h531.7c7.5 0 14.8-2.7 20.6-7.5l1.7-1.4C901.7 817.7 960 692.7 960 560c0-60.5-11.9-119.1-35.2-174.4zM761.4 836H262.6A371.12 371.12 0 0 1 140 560c0-99.4 38.7-192.8 109-263 70.3-70.3 163.7-109 263-109 99.4 0 192.8 38.7 263 109 70.3 70.3 109 163.7 109 263 0 105.6-44.5 205.5-122.6 276z"></path><path d="M762.7 340.8l-31.1-31.1a8.03 8.03 0 0 0-11.3 0l-56.6 56.6a8.03 8.03 0 0 0 0 11.3l31.1 31.1c3.1 3.1 8.2 3.1 11.3 0l56.6-56.6c3.1-3.1 3.1-8.2 0-11.3zM750 538v44c0 4.4 3.6 8 8 8h80c4.4 0 8-3.6 8-8v-44c0-4.4-3.6-8-8-8h-80c-4.4 0-8 3.6-8 8zM304.1 309.7a8.03 8.03 0 0 0-11.3 0l-31.1 31.1a8.03 8.03 0 0 0 0 11.3l56.6 56.6c3.1 3.1 8.2 3.1 11.3 0l31.1-31.1c3.1-3.1 3.1-8.2 0-11.3l-56.6-56.6zM262 530h-80c-4.4 0-8 3.6-8 8v44c0 4.4 3.6 8 8 8h80c4.4 0 8-3.6 8-8v-44c0-4.4-3.6-8-8-8z"></path></svg>
                        <span class="sidebar-text">Overview</span>
                    </a>
                    @php
                        $servers = $project->servers()->orderBy('ip')->get();
                        $assetsExpanded = isset($activeServerId) && (!isset($activeService) || strtolower($activeService) !== 'ssl');
                    @endphp
                    <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $assetsExpanded ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)] open' : 'text-gray-700 hover:bg-gray-50 close' }}" aria-expanded="{{ $assetsExpanded ? 'true' : 'false' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 32 32" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M 17 4 L 17 5.1894531 C 16.855 5.2419531 16.714063 5.3061133 16.580078 5.3789062 C 16.312109 5.5244922 16.069141 5.7085547 15.857422 5.9238281 C 15.645703 6.1391016 15.465391 6.3846484 15.326172 6.6542969 C 15.117344 7.0587695 15 7.51625 15 8 C 15 8.205 15.022109 8.4054688 15.0625 8.5996094 C 15.143281 8.9878906 15.300547 9.3503906 15.517578 9.6699219 C 15.626094 9.8296875 15.748516 9.9789844 15.884766 10.115234 C 16.021016 10.251484 16.170313 10.373906 16.330078 10.482422 C 16.809375 10.807969 17.385 11 18 11 C 18.1425 11 18.276016 11.026953 18.396484 11.076172 C 18.516953 11.125391 18.623516 11.197734 18.712891 11.287109 C 18.802266 11.376484 18.874609 11.483047 18.923828 11.603516 C 18.973047 11.723984 19 11.8575 19 12 C 19 12.4275 18.757891 12.776172 18.396484 12.923828 C 18.276016 12.973047 18.1425 13 18 13 C 17.8575 13 17.723984 12.973047 17.603516 12.923828 C 17.483047 12.874609 17.376484 12.802266 17.287109 12.712891 C 17.197734 12.623516 17.125391 12.516953 17.076172 12.396484 C 17.026953 12.276016 17 12.1425 17 12 L 15 12 C 15 12.16125 15.013828 12.320254 15.039062 12.474609 C 15.064297 12.628965 15.1 12.778281 15.148438 12.923828 C 15.29375 13.360469 15.539844 13.753262 15.857422 14.076172 C 15.963281 14.183809 16.076328 14.283828 16.197266 14.375 C 16.318203 14.466172 16.446094 14.548301 16.580078 14.621094 C 16.714063 14.693887 16.855 14.758047 17 14.810547 L 17 16 L 19 16 L 19 14.810547 C 20.16 14.390547 21 13.29 21 12 C 21 10.565 19.958594 9.3452344 18.599609 9.0625 C 18.405469 9.0221094 18.205 9 18 9 C 17.8575 9 17.723984 8.9730469 17.603516 8.9238281 C 17.483047 8.8746094 17.376484 8.8022656 17.287109 8.7128906 C 17.197734 8.6235156 17.125391 8.5169531 17.076172 8.3964844 C 17.026953 8.2760156 17 8.1425 17 8 C 17 7.8575 17.026953 7.7239844 17.076172 7.6035156 C 17.125391 7.4830469 17.197734 7.3764844 17.287109 7.2871094 C 17.465859 7.1083594 17.715 7 18 7 C 18.57 7 19 7.43 19 8 L 21 8 C 21 7.83875 20.986172 7.6797461 20.960938 7.5253906 C 20.935703 7.3710352 20.9 7.2217187 20.851562 7.0761719 C 20.560938 6.2028906 19.87 5.5044531 19 5.1894531 L 19 4 L 17 4 z M 10.515625 18 C 9.484625 18.004 8.6992188 18.265625 8.6992188 18.265625 L 8.6796875 18.273438 L 3.0078125 20.449219 L 4.9921875 26.019531 L 9.921875 24.128906 L 17.058594 28.058594 L 29.382812 22.953125 L 28.617188 21.109375 L 17.179688 25.84375 L 10.078125 21.933594 L 6.1875 23.421875 L 5.546875 21.617188 L 9.34375 20.160156 C 9.35275 20.156156 9.819625 20 10.515625 20 C 11.218625 20 12.038281 20.156062 12.738281 20.789062 L 12.751953 20.796875 L 12.755859 20.800781 C 13.786859 21.695781 14.564484 22.321156 15.646484 22.660156 C 16.728484 23.000156 17.955859 23.03925 20.005859 23.03125 L 19.996094 21.03125 C 17.984094 21.03925 16.933094 20.968 16.246094 20.75 C 15.558094 20.539 15.070125 20.159781 14.078125 19.300781 L 14.066406 19.292969 C 12.909406 18.253969 11.534625 17.996 10.515625 18 z"></path></svg>
                        <span class="sidebar-text">Assets</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform {{ $assetsExpanded ? 'rotate-180' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                    </a>
                    <div class="{{ $assetsExpanded ? 'pl-4 open' : 'hidden pl-4 close' }}" data-submenu>
                        @foreach($servers as $server)
                            @php
                                $assets = $server->assets()->orderBy('service_name')->get();
                                $srvExpanded = isset($activeServerId) && $activeServerId === $server->id && (!isset($activeService) || strtolower($activeService) !== 'ssl');
                                $activeServiceLabel = isset($activeService) ? strtolower($activeService) : null;
                            @endphp
                            <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $srvExpanded ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)]' : 'text-gray-700 hover:bg-gray-50' }}" aria-expanded="{{ $srvExpanded ? 'true' : 'false' }}">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><g id="Receipt"><g><path d="M12,21.919a1.454,1.454,0,0,1-.791-.232L9.645,20.666a.47.47,0,0,0-.439-.028l-1.776.829a1.466,1.466,0,0,1-1.4-.087,1.214,1.214,0,0,1-.581-1.02V3.641A1.217,1.217,0,0,1,6.033,2.62a1.469,1.469,0,0,1,1.4-.087l1.775.829a.469.469,0,0,0,.439-.026L11.21,2.313a1.464,1.464,0,0,1,1.581,0l1.564,1.022a.469.469,0,0,0,.44.026l1.775-.829a1.461,1.461,0,0,1,1.4.087,1.217,1.217,0,0,1,.581,1.021V20.36a1.216,1.216,0,0,1-.581,1.02,1.46,1.46,0,0,1-1.4.087L14.8,20.639a.474.474,0,0,0-.441.027l-1.564,1.021A1.448,1.448,0,0,1,12,21.919ZM9.4,19.6a1.44,1.44,0,0,1,.79.234l1.564,1.02a.464.464,0,0,0,.487,0l1.565-1.021a1.462,1.462,0,0,1,1.41-.095l1.774.828a.463.463,0,0,0,.437-.024.221.221,0,0,0,.118-.177V3.641a.219.219,0,0,0-.118-.177.461.461,0,0,0-.437-.025l-1.775.829a1.458,1.458,0,0,1-1.409-.095L12.243,3.151a.467.467,0,0,0-.486,0L10.192,4.172a1.467,1.467,0,0,1-1.41.1L7.007,3.439a.461.461,0,0,0-.437.025.219.219,0,0,0-.118.177V20.36a.221.221,0,0,0,.118.177.468.468,0,0,0,.437.024l1.776-.829A1.461,1.461,0,0,1,9.4,19.6Z"></path><path d="M15.046,7.4H8.954a.5.5,0,0,1,0-1h6.092a.5.5,0,0,1,0,1Z"></path><path d="M15.046,10.953H8.954a.5.5,0,0,1,0-1h6.092a.5.5,0,0,1,0,1Z"></path><path d="M12,14.5H8.954a.5.5,0,0,1,0-1H12a.5.5,0,0,1,0,1Z"></path></g></g></svg>
                                <span class="sidebar-text">{{ $server->ip }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform {{ $srvExpanded ? 'rotate-180' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                            </a>
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
                    </div>
                @php $utilsExpanded = isset($subMenu) && $subMenu === 'ssl'; @endphp
                <a href="javascript:void(0);" data-submenu-toggle class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $utilsExpanded ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)] open' : 'text-gray-700 hover:bg-gray-50 close' }}" aria-expanded="{{ $utilsExpanded ? 'true' : 'false' }}">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M2 18H9V20H2V18ZM2 11H11V13H2V11ZM2 4H22V6H2V4ZM20.674 13.0251L21.8301 12.634L22.8301 14.366L21.914 15.1711C21.9704 15.4386 22 15.7158 22 16C22 16.2842 21.9704 16.5614 21.914 16.8289L22.8301 17.634L21.8301 19.366L20.674 18.9749C20.2635 19.3441 19.7763 19.6295 19.2391 19.8044L19 21H17L16.7609 19.8044C16.2237 19.6295 15.7365 19.3441 15.326 18.9749L14.1699 19.366L13.1699 17.634L14.086 16.8289C14.0296 16.5614 14 16.2842 14 16C14 15.7158 14.0296 15.4386 14.086 15.1711L13.1699 14.366L14.1699 12.634L15.326 13.0251C15.7365 12.6559 16.2237 12.3705 16.7609 12.1956L17 11H19L19.2391 12.1956C19.7763 12.3705 20.2635 12.6559 20.674 13.0251ZM18 18C19.1046 18 20 17.1046 20 16C20 14.8954 19.1046 14 18 14C16.8954 14 16 14.8954 16 16C16 17.1046 16.8954 18 18 18Z"></path></svg>
                    <span class="sidebar-text">Utils</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="submenu-caret h-4 w-4 ml-auto transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="{{ $utilsExpanded ? 'pl-4 open' : 'hidden pl-4 close' }}" data-submenu>
                    @if($servers->isNotEmpty())
                        @php
                            $sslActive = isset($activeService) && $activeService === 'ssl';
                            $targetServerId = isset($activeServerId) ? $activeServerId : $servers[0]->id;
                        @endphp
                        <a href="{{ route('admin.server.ssl', $targetServerId) }}" class="flex items-center gap-2 px-3 h-9 my-1 rounded-md {{ $sslActive ? 'text-[rgb(var(--color-primary))] bg-[rgb(var(--color-primary)/.06)]' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 256 256" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M126,136a6,6,0,0,1-6,6H72a6,6,0,0,1,0-12h48A6,6,0,0,1,126,136Zm-6-38H72a6,6,0,0,0,0,12h48a6,6,0,0,0,0-12Zm110,62.62V224a6,6,0,0,1-9,5.21l-25-14.3-25,14.3a6,6,0,0,1-9-5.21V198H40a14,14,0,0,1-14-14V56A14,14,0,0,1,40,42H216a14,14,0,0,1,14,14V87.38a49.91,49.91,0,0,1,0,73.24ZM196,86a38,38,0,1,0,38,38A38,38,0,0,0,196,86ZM162,186V160.62a50,50,0,0,1,56-81.51V56a2,2,0,0,0-2-2H40a2,2,0,0,0-2,2V184a2,2,0,0,0,2,2Zm56-17.11a49.91,49.91,0,0,1-44,0v44.77l19-10.87a6,6,0,0,1,6,0l19,10.87Z"></path></svg>
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
    <div class="fixed bottom-0 left-0 right-0 w-60">
        <button id="sidebarProfileBtn" class="w-full flex items-center gap-1 px-2 py-2 rounded-lg hover:bg-gray-50">
            <img class="h-9 w-9 rounded-full" src="{{ ($adminUser && !empty($adminUser->avatar)) ? asset($adminUser->avatar) : 'https://i.pravatar.cc/80?img=5' }}" alt="{{ auth('admin')->user()->name ?? 'John Doe' }}">
            <div class="sidebar-meta flex justify-between items-center w-full">
                <div class="text-sm text-left grid grid-cols-1">
                    <div class="font-medium sidebar-text">{{ auth('admin')->user()->name ?? 'John Doe' }}</div>
                    <div class="text-gray-500 sidebar-text">Admin</div>
                </div>
                <div class="sidebar-extra ml-auto inline-block w-4 h-4 rounded-full bg-emerald-500">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M262.29 192.31a64 64 0 1 0 57.4 57.4 64.13 64.13 0 0 0-57.4-57.4zM416.39 256a154.34 154.34 0 0 1-1.53 20.79l45.21 35.46a10.81 10.81 0 0 1 2.45 13.75l-42.77 74a10.81 10.81 0 0 1-13.14 4.59l-44.9-18.08a16.11 16.11 0 0 0-15.17 1.75A164.48 164.48 0 0 1 325 400.8a15.94 15.94 0 0 0-8.82 12.14l-6.73 47.89a11.08 11.08 0 0 1-10.68 9.17h-85.54a11.11 11.11 0 0 1-10.69-8.87l-6.72-47.82a16.07 16.07 0 0 0-9-12.22 155.3 155.3 0 0 1-21.46-12.57 16 16 0 0 0-15.11-1.71l-44.89 18.07a10.81 10.81 0 0 1-13.14-4.58l-42.77-74a10.8 10.8 0 0 1 2.45-13.75l38.21-30a16.05 16.05 0 0 0 6-14.08c-.36-4.17-.58-8.33-.58-12.5s.21-8.27.58-12.35a16 16 0 0 0-6.07-13.94l-38.19-30A10.81 10.81 0 0 1 49.48 186l42.77-74a10.81 10.81 0 0 1 13.14-4.59l44.9 18.08a16.11 16.11 0 0 0 15.17-1.75A164.48 164.48 0 0 1 187 111.2a15.94 15.94 0 0 0 8.82-12.14l6.73-47.89A11.08 11.08 0 0 1 213.23 42h85.54a11.11 11.11 0 0 1 10.69 8.87l6.72 47.82a16.07 16.07 0 0 0 9 12.22 155.3 155.3 0 0 1 21.46 12.57 16 16 0 0 0 15.11 1.71l44.89-18.07a10.81 10.81 0 0 1 13.14 4.58l42.77 74a10.8 10.8 0 0 1-2.45 13.75l-38.21 30a16.05 16.05 0 0 0-6.05 14.08c.33 4.14.55 8.3.55 12.47z"></path>
                    </svg>
                </div>
            </div>
        </button>
    </div>
</aside>
