<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel | Apprise Tech Group</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        #adminSidebar {
            transition: width 200ms ease, padding 200ms ease;
            will-change: width;
        }
        [data-menu], [data-submenu] { will-change: height, opacity; }
        #adminBrandText { padding-left: 1rem; transition: opacity 150ms ease, transform 150ms ease, max-width 200ms ease; max-width: 200px; overflow: hidden; display: inline-block; }
        #adminSidebar nav a { transition: padding 200ms ease, color 150ms ease; }
        .sidebar-text { padding-left: 1rem; transition: opacity 200ms ease, transform 200ms ease, max-width 200ms ease, padding 200ms ease; max-width: 200px; overflow: hidden; display: inline-block; white-space: nowrap; }
        .sidebar-collapsed #adminSidebar { width: 4rem; overflow: visible; }
        .sidebar-collapsed #adminSidebar nav a { justify-content: flex-start; padding-left: .75rem; padding-right: .75rem; }
        .sidebar-collapsed .sidebar-text { opacity: 0; transform: translateX(-4px); pointer-events: none; max-width: 0; padding-left: 0; }
        .sidebar-collapsed #adminBrandText { opacity: 0; transform: translateX(-4px); max-width: 0; }
        .sidebar-collapsed #sidebarProfileBtn { justify-content: center; gap: 0; width: 4rem; }
        .sidebar-collapsed #sidebarProfileBtn .sidebar-meta { opacity: 0; transform: translateX(-4px); max-width: 0; transition: opacity 200ms ease, transform 200ms ease, max-width 200ms ease; overflow: hidden; }
        .sidebar-collapsed #sidebarProfileBtn .sidebar-extra { opacity: 0; transform: translateX(-4px); transition: opacity 200ms ease, transform 200ms ease; }
        .sidebar-collapsed .submenu-caret { display: none; }
    </style>
</head>
<body class="min-h-screen bg-gray-100 text-gray-900">

    <div id="adminShell" class="flex min-h-screen">

        @include('partials.sidebar')

        <div class="flex-1 flex flex-col">

            @include('partials.topbar')

            <main class="flex-1 p-6">

                @yield('content')
                
            </main>

            @include('partials.footer')

        </div>

    </div>

    <div id="toastRoot" class="fixed z-[100] top-4 right-4 space-y-2 pointer-events-none"></div>
    @php
        $flash = [];
        if (session('success')) $flash[] = ['type' => 'success', 'text' => session('success')];
        if (session('error')) $flash[] = ['type' => 'error', 'text' => session('error')];
        if (session('warning')) $flash[] = ['type' => 'warning', 'text' => session('warning')];
        if (session('info')) $flash[] = ['type' => 'info', 'text' => session('info')];
        if ($errors && $errors->any()) {
            foreach ($errors->all() as $e) {
                $flash[] = ['type' => 'error', 'text' => $e];
            }
        }
    @endphp
    <script>
        window.__flash = {!! json_encode($flash) !!};
    </script>
</body>
</html>
