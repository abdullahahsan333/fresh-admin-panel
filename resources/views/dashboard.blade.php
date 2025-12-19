<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-2">Dashboard</h1>
        <a href="{{ route('logout') }}" class="text-sm underline">Logout</a>
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
