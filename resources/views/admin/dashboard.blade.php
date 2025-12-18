<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
<body class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-2">Admin Dashboard</h1>
        <a href="{{ route('admin.logout') }}" class="text-sm underline">Logout</a>
    </div>
</body>
</html>
